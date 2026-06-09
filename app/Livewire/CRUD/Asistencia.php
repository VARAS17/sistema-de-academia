<?php

namespace App\Livewire\CRUD;

use App\Models\Ciclo;
use App\Models\ControlAsistencia;
use App\Models\Asistencia as Marcacion;
use App\Models\Alumno;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Asistencia extends Component
{
    public $step = 1;
    public $breadcrumb = [];
    public $cicloSeleccionado;
    public $areaSeleccionada;
    public $controlSeleccionado;
    public $fecha, $turno = 'mañana';
    public $search = '';

    // Propiedades para rol de alumno
    public $esAlumno = false;
    public $alumnoPerfil = null;

    public function mount()
    {
        $this->fecha = date('Y-m-d');
        
        // Detectar si el usuario es un alumno
        $this->alumnoPerfil = Alumno::where('user_id', Auth::id())->first();
        
        if ($this->alumnoPerfil) {
            $this->esAlumno = true;
            $this->seleccionarCiclo($this->alumnoPerfil->ciclo_id);
        }

        $this->setBreadcrumb();
    }

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

        if ($this->step == 3 && $this->controlSeleccionado) {
            $this->breadcrumb[] = [
                'name' => "Clase: " . date('d/m/y', strtotime($this->controlSeleccionado->fecha)), 
                'step' => 3
            ];
        }
    }

    public function goToStep($step)
    {
        if ($this->esAlumno && $step == 1) return;

        $this->step = $step;
        if ($step == 1) {
            $this->reset(['cicloSeleccionado', 'areaSeleccionada', 'controlSeleccionado']);
        }
        if ($step == 2) {
            $this->reset(['controlSeleccionado']);
        }
        $this->setBreadcrumb();
    }

    public function seleccionarCiclo($cicloId)
    {
        if ($this->esAlumno && $this->alumnoPerfil->ciclo_id != $cicloId) {
            abort(403);
        }

        $ciclo = Ciclo::with('area')->findOrFail($cicloId);
        $this->cicloSeleccionado = $ciclo->toArray();
        $this->areaSeleccionada = $ciclo->area->toArray();
        $this->step = 2;
        $this->setBreadcrumb();
    }

    public function crearControl()
    {
        if ($this->esAlumno) return;

        $this->validate([
            'fecha' => 'required|date',
            'turno' => 'required|in:mañana,tarde,noche',
        ]);

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

    public function abrirControl($id)
    {
        $control = ControlAsistencia::findOrFail($id);
        
        if ($this->esAlumno && $control->ciclo_id != $this->alumnoPerfil->ciclo_id) {
            abort(403);
        }

        $this->controlSeleccionado = $control;
        $this->step = 3;
        $this->setBreadcrumb();
    }

    public function marcarAsistencia($userId, $nuevoEstado)
    {
        if($this->controlSeleccionado->estado == 'cerrado') return;

        if ($this->esAlumno) {
            if ($userId != Auth::id()) return;
            $nuevoEstado = 'presente'; 
        }

        Marcacion::updateOrCreate(
            [
                'control_asistencia_id' => $this->controlSeleccionado->id,
                'alumno_id' => $userId // Aquí usamos el user_id que llega del front
            ],
            [
                'estado' => $nuevoEstado,
                'hora_marcado' => now()->toTimeString()
            ]
        );
    }

    public function cerrarControl()
    {
        if ($this->esAlumno) return;
        $this->controlSeleccionado->update(['estado' => 'cerrado']);
    }

    public function render()
    {
        return view('livewire.c-r-u-d.asistencia', [
            'ciclos' => $this->esAlumno ? [] : Ciclo::with('area')->activos()->get(),
            
            'controles' => ($this->step == 2) 
                ? ControlAsistencia::where('ciclo_id', $this->cicloSeleccionado['id'])
                    ->orderBy('fecha', 'desc')
                    ->get()
                : [],

            'alumnos' => ($this->step == 3)
                ? Alumno::where('ciclo_id', $this->cicloSeleccionado['id'])
                    ->when($this->esAlumno, function($q) {
                        return $q->where('user_id', Auth::id());
                    })
                    ->when(!$this->esAlumno && $this->search, function($q) {
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