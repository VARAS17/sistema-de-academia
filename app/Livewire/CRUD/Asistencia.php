<?php

namespace App\Livewire\CRUD;

use App\Models\Ciclo;
use App\Models\ControlAsistencia;
use App\Models\Asistencia as Marcacion;
use App\Models\Alumno;
use App\Models\Matricula;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Asistencia extends Component
{
    // Propiedades de Navegación
    public $step = 1;
    public $breadcrumb = [];
    
    // Propiedades de Datos Seleccionados
    public $cicloSeleccionado;
    public $areaSeleccionada;
    public $controlSeleccionado;
    
    // Propiedades para Formulario y Filtros
    public $fecha, $turno = 'mañana';
    public $search = '';

    // Propiedades de Estado de Usuario
    public $esAlumno = false;
    public $alumnoPerfil = null;
    public $alumnoCicloId = null; // Ciclo obtenido de su matrícula activa
    public $confirmandoCierre = false;

    /**
     * Inicialización del Componente
     */
    public function mount()
    {
        $this->fecha = date('Y-m-d');
        
        // 1. Identificar si el usuario logueado es un Alumno
        $this->alumnoPerfil = Alumno::where('user_id', Auth::id())->first();
        
        if ($this->alumnoPerfil) {
            $this->esAlumno = true;

            // 2. Buscar el ciclo en su matrícula activa (Ya no usamos Alumno->ciclo_id)
            $matriculaActiva = Matricula::where('alumno_id', Auth::id())
                                ->where('estado', 'Activa')
                                ->latest()
                                ->first();

            if ($matriculaActiva) {
                $this->alumnoCicloId = $matriculaActiva->ciclo_id;
                // Si es alumno, entra directo a su ciclo
                $this->seleccionarCiclo($this->alumnoCicloId);
            } else {
                // Si no tiene matrícula activa, se queda en el paso 1 o podrías mostrar un aviso
                $this->step = 1;
            }
        }

        $this->setBreadcrumb();
    }

    /**
     * Gestión de Breadcrumbs (Migas de pan)
     */
    public function setBreadcrumb()
    {
        if ($this->esAlumno) {
            $this->breadcrumb = [['name' => 'Mi Asistencia', 'step' => 2]];
        } else {
            $this->breadcrumb = [['name' => 'Inicio', 'step' => 1]];
        }

        if ($this->step >= 2 && $this->cicloSeleccionado) {
            $nombre = $this->esAlumno ? "Historial" : "{$this->cicloSeleccionado['nombre']}";
            $this->breadcrumb[] = ['name' => $nombre, 'step' => 2];
        }

        if ($this->step == 4) {
            $this->breadcrumb[] = ['name' => 'Nueva Sesión', 'step' => 4];
        }

        if ($this->step == 3 && $this->controlSeleccionado) {
            $this->breadcrumb[] = [
                'name' => "Clase: " . date('d/m/y', strtotime($this->controlSeleccionado->fecha)), 
                'step' => 3
            ];
        }
    }

    /**
     * Navegación entre pasos
     */
    public function goToStep($step)
    {
        // Seguridad: Alumnos no pueden ir al paso 1 (lista de ciclos) ni al 4 (crear)
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

    /**
     * Seleccionar un Ciclo Académico
     */
    public function seleccionarCiclo($cicloId)
    {
        // Seguridad: El alumno solo puede ver SU ciclo matriculado
        if ($this->esAlumno && $this->alumnoCicloId != $cicloId) {
            abort(403, 'No tienes permiso para acceder a este ciclo.');
        }

        $ciclo = Ciclo::with('area')->findOrFail($cicloId);
        $this->cicloSeleccionado = $ciclo->toArray();
        $this->areaSeleccionada = $ciclo->area->toArray();
        $this->step = 2;
        $this->setBreadcrumb();
    }

    /**
     * Lógica para Administradores: Crear nueva sesión de asistencia
     */
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

        $this->validate([
            'fecha' => 'required|date',
            'turno' => 'required|in:mañana,tarde,noche',
        ]);

        // Evitar duplicados para el mismo día, turno y ciclo
        $existe = ControlAsistencia::where([
            'ciclo_id' => $this->cicloSeleccionado['id'],
            'fecha' => $this->fecha,
            'turno' => $this->turno
        ])->first();

        if ($existe) {
            $this->abrirControl($existe->id);
            return;
        }

        $nuevoControl = ControlAsistencia::create([
            'ciclo_id' => $this->cicloSeleccionado['id'],
            'area_id' => $this->areaSeleccionada['id'],
            'user_id' => Auth::id(),
            'fecha' => $this->fecha,
            'turno' => $this->turno,
            'estado' => 'abierto'
        ]);

        $this->abrirControl($nuevoControl->id);
    }

    /**
     * Abrir una sesión de asistencia específica
     */
    public function abrirControl($id)
    {
        $control = ControlAsistencia::findOrFail($id);
        
        // Seguridad: El alumno solo puede ver sesiones de su propio ciclo
        if ($this->esAlumno && $control->ciclo_id != $this->alumnoCicloId) {
            abort(403, 'Acceso denegado.');
        }

        $this->controlSeleccionado = $control;
        $this->step = 3;
        $this->setBreadcrumb();
    }

    /**
     * Marcar Asistencia (P / T / F)
     */
    public function marcarAsistencia($userId, $nuevoEstado)
    {
        // No marcar si el control está cerrado
        if($this->controlSeleccionado->estado == 'cerrado') return;

        // Seguridad: Si es alumno, solo puede marcarse a SÍ MISMO como presente
        if ($this->esAlumno) {
            if ($userId != Auth::id()) return;
            $nuevoEstado = 'presente'; 
        }

        Marcacion::updateOrCreate(
            [
                'control_asistencia_id' => $this->controlSeleccionado->id,
                'alumno_id' => $userId 
            ],
            [
                'estado' => $nuevoEstado,
                'hora_marcado' => now()->toTimeString()
            ]
        );
    }

    /**
     * Cierre de Sesión (Finalizar día)
     */
    public function abrirConfirmacionCierre()
    {
        if ($this->esAlumno) return;
        $this->confirmandoCierre = true;
    }

    public function cerrarConfirmacionCierre()
    {
        $this->confirmandoCierre = false;
    }

    public function cerrarControl()
    {
        if ($this->esAlumno) return;

        $this->controlSeleccionado->update(['estado' => 'cerrado']);
        $this->confirmandoCierre = false;
        $this->setBreadcrumb();
    }

    /**
     * Renderizado de la Vista con Filtros Académicos
     */
    public function render()
    {
        return view('livewire.c-r-u-d.asistencia', [
            // Ciclos: Solo se muestran al Admin
            'ciclos' => $this->esAlumno ? [] : Ciclo::with('area')->get(),
            
            // Lista de sesiones del ciclo seleccionado
            'controles' => ($this->step == 2) 
                ? ControlAsistencia::where('ciclo_id', $this->cicloSeleccionado['id'])
                    ->orderBy('fecha', 'desc')
                    ->get()
                : [],

            // Lista de Alumnos: Filtrado por Matrícula en lugar de columna ciclo_id directa
            'alumnos' => ($this->step == 3)
                ? Alumno::whereHas('matriculas', function($q) {
                        $q->where('ciclo_id', $this->cicloSeleccionado['id'])
                          ->where('estado', 'Activa');
                    })
                    ->when($this->esAlumno, function($q) {
                        // El alumno solo se ve a sí mismo
                        return $q->where('user_id', Auth::id());
                    })
                    ->when(!$this->esAlumno && $this->search, function($q) {
                        // Búsqueda para el admin
                        $q->where(function($sub) {
                            $sub->where('dni', 'like', "%{$this->search}%")
                                ->orWhereHas('user', function($u) {
                                    $u->where('name', 'like', "%{$this->search}%");
                                });
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