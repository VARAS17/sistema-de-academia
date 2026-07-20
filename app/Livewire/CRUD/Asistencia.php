<?php

namespace App\Livewire\CRUD;

use App\Models\Ciclo;
use App\Models\ControlAsistencia;
use App\Models\Asistencia as Marcacion;
use App\Models\Alumno;
use App\Models\Matricula;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Asistencia extends Component
{
    // Propiedades de Navegación y Estado
    public $step = 1;
    public $breadcrumb = [];
    public $modoLectura = false; // Define si el Paso 3 es solo vista o edición
    
    // Datos Seleccionados
    public $cicloSeleccionado;
    public $areaSeleccionada;
    public $controlSeleccionado;
    
    // Formulario y Filtros
    public $fecha, $turno = 'mañana';
    public $search = '';

    // Estado de Usuario y Modales
    public $esAlumno = false;
    public $alumnoPerfil = null;
    public $confirmandoCierre = false;
    public $confirmandoEliminacion = false;
    public $idAEliminar = null;

    public function mount()
    {
        $this->fecha = date('Y-m-d');
        
        // 1. Identificar si el usuario es Alumno
        $this->alumnoPerfil = Alumno::where('user_id', Auth::id())->first();
        
        if ($this->alumnoPerfil) {
            $this->esAlumno = true;

            // Buscar matrícula activa para redirigir directo a sus asistencias
            $matriculaActiva = Matricula::where('alumno_id', $this->alumnoPerfil->user_id)
                                ->where('estado', 'Activa')
                                ->latest()
                                ->first();

            if ($matriculaActiva) {
                // El alumno entra directo al historial de su ciclo (Paso 2)
                $this->seleccionarCiclo($matriculaActiva->ciclo_id);
            }
        }

        $this->setBreadcrumb();
    }

    /**
     * Breadcrumbs dinámicos:
     * Reflejan si se está visualizando o editando una asistencia.
     */
    public function setBreadcrumb()
    {
        $this->breadcrumb = [];

        // Nivel 1: Base
        $this->breadcrumb[] = ['name' => 'Asistencia', 'step' => $this->esAlumno ? 2 : 1];

        // Nivel 2: Ciclo seleccionado (Solo Admin)
        if (!$this->esAlumno && $this->step >= 2 && $this->cicloSeleccionado) {
            $this->breadcrumb[] = [
                'name' => $this->cicloSeleccionado['nombre'], 
                'step' => 2
            ];
        }

        // Nivel 3: Detalle de la clase con indicador de modo
        if ($this->step == 3 && $this->controlSeleccionado) {
            $prefijo = $this->modoLectura ? 'Ver' : 'Editar';
            $this->breadcrumb[] = [
                'name' => $prefijo . ": " . date('d/m/y', strtotime($this->controlSeleccionado->fecha)), 
                'step' => 3
            ];
        }

        if ($this->step == 4) {
            $this->breadcrumb[] = ['name' => 'Nueva Sesión', 'step' => 4];
        }
    }

    public function goToStep($step)
    {
        // Seguridad: Alumno no puede ir a pasos de admin
        if ($this->esAlumno && ($step == 1 || $step == 4)) return;

        $this->step = $step;
        
        if ($step == 1) {
            $this->reset(['cicloSeleccionado', 'areaSeleccionada', 'controlSeleccionado', 'modoLectura']);
        }
        if ($step == 2) {
            $this->reset(['controlSeleccionado', 'modoLectura', 'confirmandoCierre', 'confirmandoEliminacion']);
        }
        
        $this->setBreadcrumb();
    }

    public function seleccionarCiclo($cicloId)
    {
        $ciclo = Ciclo::with('area')->findOrFail($cicloId);
        $this->cicloSeleccionado = $ciclo->toArray();
        $this->areaSeleccionada = $ciclo->area ? $ciclo->area->toArray() : [];
        $this->step = 2;
        $this->setBreadcrumb();
    }

    // --- ACCIONES DEL ADMINISTRADOR EN PASO 2 ---

    public function verAsistencia($id)
    {
        $this->controlSeleccionado = ControlAsistencia::findOrFail($id);
        $this->modoLectura = true;
        $this->step = 3;
        $this->setBreadcrumb();
    }

    public function editarAsistencia($id)
    {
        $this->controlSeleccionado = ControlAsistencia::findOrFail($id);
        
        // Bloquear si la sesión ya está cerrada
        if ($this->controlSeleccionado->estado == 'cerrado') {
            session()->flash('error', 'No se puede editar una sesión cerrada.');
            return;
        }

        $this->modoLectura = false;
        $this->step = 3;
        $this->setBreadcrumb();
    }

    public function abrirConfirmacionEliminacion($id)
    {
        $this->idAEliminar = $id;
        $this->confirmandoEliminacion = true;
    }

    public function eliminarControl()
    {
        if ($this->esAlumno) return;

        if ($this->idAEliminar) {
            // Se eliminan en cascada si la relación está configurada, 
            // sino Livewire/Eloquent se encarga.
            ControlAsistencia::destroy($this->idAEliminar);
            $this->confirmandoEliminacion = false;
            $this->idAEliminar = null;
        }
    }

    // --- GESTIÓN DE MARCACIÓN (PASO 3) ---

    public function marcarAsistencia($alumnoId, $nuevoEstado)
    {
        if ($this->esAlumno || $this->modoLectura) return;
        if ($this->controlSeleccionado->estado == 'cerrado') return;

        Marcacion::updateOrCreate(
            [
                'control_asistencia_id' => $this->controlSeleccionado->id,
                'alumno_id' => $alumnoId
            ],
            [
                'estado' => $nuevoEstado,
                'hora_marcado' => now()->toTimeString()
            ]
        );
    }

    // --- REGISTRO DE NUEVA SESIÓN (PASO 4) ---

    public function mostrarFormularioCreacion()
    {
        if ($this->esAlumno) return;
        $this->fecha = date('Y-m-d');
        $this->step = 4;
        $this->setBreadcrumb();
    }

    public function guardarControl()
    {
        if ($this->esAlumno) return;

        $this->validate(['fecha' => 'required|date', 'turno' => 'required']);

        $control = ControlAsistencia::updateOrCreate(
            [
                'ciclo_id' => $this->cicloSeleccionado['id'],
                'fecha' => $this->fecha,
                'turno' => $this->turno
            ],
            [
                'area_id' => $this->areaSeleccionada['id'],
                'user_id' => Auth::id(),
                'estado' => 'abierto'
            ]
        );

        // Al guardar, entra directo a editar la asistencia
        $this->editarAsistencia($control->id);
    }

    // --- CIERRE DE SESIÓN ---

    public function cerrarControl()
    {
        if ($this->esAlumno) return;
        
        $this->controlSeleccionado->update(['estado' => 'cerrado']);
        $this->confirmandoCierre = false;
        $this->modoLectura = true; // Cambiar a vista de lectura tras cerrar
        $this->setBreadcrumb();
    }

    public function render()
    {
        return view('livewire.c-r-u-d.asistencia', [
            'ciclos' => $this->esAlumno ? [] : Ciclo::with('area')->get(),
            
            // Historial de asistencias del ciclo
            'controles' => ($this->step == 2) 
                ? ControlAsistencia::where('ciclo_id', $this->cicloSeleccionado['id'])
                    ->with('asistencias') // Importante para la vista rápida del alumno
                    ->orderBy('fecha', 'desc')->get()
                : [],

            // Lista de alumnos para marcar o ver detalle
            'alumnos' => ($this->step == 3)
                ? Alumno::whereHas('matriculas', function($q) {
                        $q->where('ciclo_id', $this->cicloSeleccionado['id'])
                          ->where('estado', 'Activa');
                    })
                    ->when($this->search, function($q) {
                        $q->where(function($sub) {
                            $sub->where('dni', 'like', "%{$this->search}%")
                                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
                        });
                    })
                    ->with(['user', 'asistencias' => function($q) {
                        $q->where('control_asistencia_id', $this->controlSeleccionado->id);
                    }])
                    ->get()
                : []
        ]);
    }
}