<?php

namespace App\Livewire\CRUD;

use App\Models\Area;
use App\Models\Ciclo;
use App\Models\Simulacro;
use App\Models\Alumno;
use App\Models\ResultadoSimulacro;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Puntajesimulacro extends Component
{
    use WithPagination;

    // Propiedades de Filtro y Navegación
    public $area_id, $ciclo_id, $simulacro_id;
    public $search = '';
    public $sortScore = 'desc'; // Orden predeterminado por puntaje
    public $view = 'list'; // Estados: list, edit, show

    // Propiedades para el Alumno Seleccionado (Edición/Vista)
    public $selectedAlumno;
    public $correctas = 0, $incorrectas = 0, $blanco = 100, $puntaje = 0.000;
    public $error_suma = false;

    // Propiedad para el Alumno (Vista Historial)
    public $misResultados = [];

    // Propiedad para Modal de Eliminación
    public $alumnoIdParaEliminar;

    protected $queryString = [
        'area_id' => ['except' => ''],
        'ciclo_id' => ['except' => ''],
        'simulacro_id' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        if (auth()->user()->hasRole('alumno')) {
            $this->cargarNotasAlumno();
        }
    }

    /**
     * NAVEGACIÓN Y RESETS
     */
    public function setView($mode, $alumnoId = null)
    {
        $this->view = $mode;
        
        if ($mode === 'edit' || $mode === 'show') {
            $this->cargarDatosAlumno($alumnoId);
        } else {
            $this->resetIndividualFields();
        }
    }

    private function resetIndividualFields()
    {
        $this->selectedAlumno = null;
        $this->correctas = 0;
        $this->incorrectas = 0;
        $this->blanco = 100;
        $this->puntaje = 0.000;
    }

    /**
     * CARGA DE DATOS
     */
    public function cargarDatosAlumno($userId)
    {
        $this->selectedAlumno = Alumno::with('user')->where('user_id', $userId)->first();
        
        $existente = ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
            ->where('alumno_id', $userId)
            ->first();

        if ($existente) {
            $this->correctas = $existente->correctas;
            $this->incorrectas = $existente->incorrectas;
            $this->blanco = $existente->blanco;
            $this->puntaje = $existente->puntaje;
        } else {
            $this->correctas = 0;
            $this->incorrectas = 0;
            $this->blanco = 100;
            $this->puntaje = 0.000;
        }
        $this->calcular();
    }

    /**
     * CÁLCULO EN TIEMPO REAL (REGLA DE LAS 100 PREGUNTAS)
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['correctas', 'incorrectas', 'blanco'])) {
            $this->calcular();
        }
    }

    public function calcular()
    {
        // Sanitizar entradas
        $c = max(0, min(100, (int)$this->correctas));
        $i = max(0, min(100, (int)$this->incorrectas));
        $b = max(0, min(100, (int)$this->blanco));

        $this->correctas = $c;
        $this->incorrectas = $i;
        $this->blanco = $b;

        $suma = $c + $i + $b;
        $this->error_suma = ($suma !== 100);

        // Fórmula de puntaje
        $calc = ($c * 4.025) - ($i * 0.975);
        $this->puntaje = number_format($calc, 3, '.', '');

        if ($this->error_suma) {
            session()->flash('error_suma', "Atención: La suma es $suma/100.");
        }
    }

    /**
     * PERSISTENCIA DE DATOS
     */
    public function save()
    {
        if ($this->error_suma) {
            session()->flash('error', 'No se puede guardar. La suma de preguntas debe ser exactamente 100.');
            return;
        }

        try {
            DB::transaction(function () {
                ResultadoSimulacro::updateOrCreate(
                    [
                        'simulacro_id' => $this->simulacro_id,
                        'alumno_id' => $this->selectedAlumno->user_id
                    ],
                    [
                        'correctas' => $this->correctas,
                        'incorrectas' => $this->incorrectas,
                        'blanco' => $this->blanco,
                        'puntaje' => $this->puntaje,
                    ]
                );
                $this->recalcularRanking();
            });

            session()->flash('message', 'Puntaje actualizado correctamente.');
            $this->setView('list');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * ELIMINACIÓN (RESET A 0)
     */
    public function confirmarEliminacion($userId)
    {
        $this->alumnoIdParaEliminar = $userId;
        $this->dispatch('show-delete-modal'); // Dispara evento para JS/Alpine
    }

    public function deletePuntaje()
    {
        if (!$this->alumnoIdParaEliminar) return;

        ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
            ->where('alumno_id', $this->alumnoIdParaEliminar)
            ->update([
                'correctas' => 0,
                'incorrectas' => 0,
                'blanco' => 0,
                'puntaje' => 0,
            ]);

        $this->recalcularRanking();
        $this->alumnoIdParaEliminar = null;
        session()->flash('message', 'Puntaje reseteado a cero.');
    }

    private function recalcularRanking()
    {
        $resultados = ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
            ->orderBy('puntaje', 'desc')
            ->get();

        foreach ($resultados as $indice => $res) {
            $res->update(['puesto' => $indice + 1]);
        }
    }

    /**
     * FILTROS DINÁMICOS
     */
    public function updatedAreaId() { $this->reset(['ciclo_id', 'simulacro_id', 'view']); }
    public function updatedCicloId() { $this->reset(['simulacro_id', 'view']); }
    public function updatedSimulacroId() { $this->view = 'list'; }

    public function toggleSort()
    {
        $this->sortScore = ($this->sortScore === 'desc') ? 'asc' : 'desc';
    }

    public function cargarNotasAlumno()
    {
        $this->misResultados = ResultadoSimulacro::with(['simulacro.area', 'simulacro.ciclo'])
            ->where('alumno_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        $alumnos = collect();

        if ($this->simulacro_id && $this->view === 'list') {
            $alumnos = Alumno::with(['user', 'resultados' => function($q) {
                    $q->where('simulacro_id', $this->simulacro_id);
                }])
                ->whereHas('matriculas', function($q) {
                    $q->where('ciclo_id', $this->ciclo_id)->where('estado', 'Activa');
                })
                ->when($this->search, function($q) {
                    $q->where(function($sub) {
                        $sub->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                            ->orWhere('dni', 'like', '%'.$this->search.'%');
                    });
                })
                // Ordenamiento por puntaje mediante el JOIN con resultados
                ->leftJoin('resultados_simulacros', function($join) {
                    $join->on('alumnos.user_id', '=', 'resultados_simulacros.alumno_id')
                         ->where('resultados_simulacros.simulacro_id', '=', $this->simulacro_id);
                })
                ->select('alumnos.*', 'resultados_simulacros.puntaje as puntaje_sort')
                ->orderBy('puntaje_sort', $this->sortScore)
                ->paginate(15);
        }

        return view('livewire.c-r-u-d.puntajesimulacro', [
            'areas' => Area::all(),
            'ciclos' => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect(),
            'simulacros' => $this->ciclo_id ? Simulacro::where('ciclo_id', $this->ciclo_id)->get() : collect(),
            'alumnos' => $alumnos
        ]);
    }
}