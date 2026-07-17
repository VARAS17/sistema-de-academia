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
    // Propiedades de Navegación
    public $step = 1;
    public $breadcrumb = [];
    
    // Datos Seleccionados
    public $cicloSeleccionado;
    public $areaSeleccionada;
    public $controlSeleccionado;
    
    // Formulario y Filtros
    public $fecha, $turno = 'mañana';
    public $search = '';

    // Estado de Usuario
    public $esAlumno = false;
    public $alumnoPerfil = null;
    public $alumnoCicloId = null; 
    public $confirmandoCierre = false;

    public function mount()
    {
        $this->fecha = date('Y-m-d');
        
        // 1. Identificar si es Alumno
        $this->alumnoPerfil = Alumno::where('user_id', Auth::id())->first();
        
        if ($this->alumnoPerfil) {
            $this->esAlumno = true;

            // Buscar matrícula activa para obtener el ciclo
            $matriculaActiva = Matricula::where('alumno_id', $this->alumnoPerfil->user_id)
                                ->where('estado', 'Activa')
                                ->latest()
                                ->first();

            if ($matriculaActiva) {
                $this->alumnoCicloId = $matriculaActiva->ciclo_id;
                // Alumno entra directo a su historial (Paso 2)
                $this->seleccionarCiclo($this->alumnoCicloId);
            }
        }

        $this->setBreadcrumb();
    }

    /**
     * Gestión de Breadcrumbs según requerimiento:
     * Admin: Asistencia -> Ciclo (Área) -> Clase
     * Alumno: Asistencia -> Clase
     */
    public function setBreadcrumb()
    {
        // El "Inicio" se maneja fijo en el HTML apuntando al dashboard
        $this->breadcrumb = [];

        // Nivel 1: El nombre base de la sección
        $this->breadcrumb[] = ['name' => 'Asistencia', 'step' => $this->esAlumno ? 2 : 1];

        // Nivel 2: Para Admin mostrar "Ciclo (Área)"
        if (!$this->esAlumno && $this->step >= 2 && $this->cicloSeleccionado) {
            $this->breadcrumb[] = [
                'name' => $this->cicloSeleccionado['nombre'] . " (" . ($this->areaSeleccionada['nombre'] ?? '') . ")", 
                'step' => 2
            ];
        }

        // Nivel 3: Detalle de la clase (Fecha)
        if ($this->step == 3 && $this->controlSeleccionado) {
            $this->breadcrumb[] = [
                'name' => "Clase: " . date('d/m/y', strtotime($this->controlSeleccionado->fecha)), 
                'step' => 3
            ];
        }

        if ($this->step == 4) {
            $this->breadcrumb[] = ['name' => 'Nueva Sesión', 'step' => 4];
        }
    }

    public function goToStep($step)
    {
        if ($this->esAlumno && ($step == 1 || $step == 4)) return;

        $this->step = $step;
        if ($step == 1) {
            $this->reset(['cicloSeleccionado', 'areaSeleccionada', 'controlSeleccionado', 'confirmandoCierre']);
        }
        if ($step == 2) {
            $this->reset(['controlSeleccionado', 'confirmandoCierre']);
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

    public function abrirControl($id)
    {
        $this->controlSeleccionado = ControlAsistencia::findOrFail($id);
        $this->step = 3;
        $this->setBreadcrumb();
    }

    /**
     * Marcar Asistencia: Corregido para permitir cambios de estado por el Admin
     */
    public function marcarAsistencia($userId, $nuevoEstado)
    {
        // 1. Seguridad: Solo el administrador puede usar esta función
        if ($this->esAlumno) return;

        // 2. No permitir cambios si la sesión está cerrada
        if($this->controlSeleccionado->estado == 'cerrado') return;

        // 3. Actualizar o Crear Marcación
        // Se busca por el ID del alumno (no del usuario)
        Marcacion::updateOrCreate(
            [
                'control_asistencia_id' => $this->controlSeleccionado->id,
                'alumno_id' => $userId
            ],
            [
                'estado' => $nuevoEstado, // Aquí se guarda lo que el admin elija (P, T o F)
                'hora_marcado' => now()->toTimeString()
            ]
        );
    }

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

        $this->abrirControl($control->id);
    }

    public function abrirConfirmacionCierre() { $this->confirmandoCierre = true; }
    public function cerrarConfirmacionCierre() { $this->confirmandoCierre = false; }

    public function cerrarControl()
    {
        if ($this->esAlumno) return;
        $this->controlSeleccionado->update(['estado' => 'cerrado']);
        $this->confirmandoCierre = false;
        $this->setBreadcrumb();
    }

    public function render()
    {
        return view('livewire.c-r-u-d.asistencia', [
            'ciclos' => $this->esAlumno ? [] : Ciclo::with('area')->get(),
            
            'controles' => ($this->step == 2) 
                ? ControlAsistencia::where('ciclo_id', $this->cicloSeleccionado['id'])
                    ->orderBy('fecha', 'desc')->get()
                : [],

            'alumnos' => ($this->step == 3)
                ? Alumno::whereHas('matriculas', function($q) {
                        $q->where('ciclo_id', $this->cicloSeleccionado['id'])
                          ->where('estado', 'Activa');
                    })
                    ->when($this->esAlumno, function($q) {
                        // El alumno solo se ve a sí mismo usando su ID de la tabla Alumnos
                        return $q->where('user_id', $this->alumnoPerfil->user_id);
                    })
                    ->when(!$this->esAlumno && $this->search, function($q) {
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