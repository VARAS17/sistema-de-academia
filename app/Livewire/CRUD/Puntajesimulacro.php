<?php

namespace App\Livewire\CRUD;

use App\Models\Area;
use App\Models\Ciclo;
use App\Models\Simulacro;
use App\Models\Alumno;
use App\Models\ResultadoSimulacro;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Puntajesimulacro extends Component
{
    // Propiedades de navegación/filtro
    public $area_id;
    public $ciclo_id;
    public $simulacro_id;
    
    // Matriz de entrada para Admin/Docente
    public $resultados = []; 

    // Propiedad para vista de Alumno
    public $misResultados = [];

    protected function rules()
    {
        return [
            'area_id' => 'required|exists:areas,id',
            'ciclo_id' => 'required|exists:ciclos,id',
            'simulacro_id' => 'required|exists:simulacros,id',
            'resultados.*.correctas' => 'required|integer|min:0|max:100',
            'resultados.*.incorrectas' => 'required|integer|min:0|max:100',
            'resultados.*.blanco' => 'required|integer|min:0|max:100',
            'resultados.*.puntaje' => 'required|numeric',
        ];
    }

    public function mount()
    {
        if (auth()->user()->hasRole('alumno')) {
            $this->cargarNotasAlumno();
        }
    }

    /**
     * HOOK DE LIVEWIRE: Se activa al cambiar valores en el array $resultados
     */
    public function updatedResultados($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) >= 2) {
            $alumno_id = $parts[0];
            $this->calcular($alumno_id);
        }
    }

    /**
     * FILTROS EN CASCADA
     */
    public function updatedAreaId()
    {
        $this->reset(['ciclo_id', 'simulacro_id', 'resultados']);
    }

    public function updatedCicloId()
    {
        $this->reset(['simulacro_id', 'resultados']);
    }

    public function updatedSimulacroId()
    {
        if (!auth()->user()->hasRole('alumno')) {
            $this->cargarAlumnosParaEdicion();
        }
    }

    public function cargarAlumnosParaEdicion()
    {
        if (!$this->simulacro_id || !$this->ciclo_id) {
            $this->resultados = [];
            return;
        }

        $alumnos = Alumno::with('user')
            ->whereHas('matriculas', function($q) {
                $q->where('ciclo_id', $this->ciclo_id)
                  ->where('estado', 'Activa');
            })
            ->get();

        $this->resultados = [];

        foreach ($alumnos as $alumno) {
            $existente = ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
                ->where('alumno_id', $alumno->user_id)
                ->first();

            $this->resultados[$alumno->user_id] = [
                'nombre' => $alumno->user->name,
                'dni' => $alumno->dni,
                'correctas' => (int)($existente->correctas ?? 0),
                'incorrectas' => (int)($existente->incorrectas ?? 0),
                'blanco' => (int)($existente->blanco ?? 0),
                'puntaje' => $existente->puntaje ?? 0.000,
                'error_suma' => false,
            ];
            
            // Forzar cálculo inicial para validar si lo cargado de DB ya suma 100
            $this->calcular($alumno->user_id);
        }
    }

    /**
     * PROCESAMIENTO Y CÁLCULO ESTRICTO (SUMA = 100)
     */
    public function calcular($alumno_id)
    {
        // 1. Sanitización: Cada campo individual entre 0 y 100
        $c = max(0, min(100, intval($this->resultados[$alumno_id]['correctas'] ?? 0)));
        $i = max(0, min(100, intval($this->resultados[$alumno_id]['incorrectas'] ?? 0)));
        $b = max(0, min(100, intval($this->resultados[$alumno_id]['blanco'] ?? 0)));
        
        $this->resultados[$alumno_id]['correctas'] = $c;
        $this->resultados[$alumno_id]['incorrectas'] = $i;
        $this->resultados[$alumno_id]['blanco'] = $b;

        $totalPreguntas = $c + $i + $b;

        // 2. REGLA ESTRICTA: Debe sumar exactamente 100
        if ($totalPreguntas !== 100) {
            $this->resultados[$alumno_id]['error_suma'] = true;
            
            // Calculamos el puntaje de todos modos para que vean el resultado, 
            // pero el error_suma bloqueará el guardado.
            $puntaje = ($c * 4.025) - ($i * 0.975);
            $this->resultados[$alumno_id]['puntaje'] = number_format($puntaje, 3, '.', '');

            $motivo = ($totalPreguntas > 100) ? "Exceso" : "Faltan";
            session()->flash("error_row_$alumno_id", "$motivo: $totalPreguntas/100");
        } else {
            // Suma perfecta de 100
            $this->resultados[$alumno_id]['error_suma'] = false;
            $puntaje = ($c * 4.025) - ($i * 0.975);
            $this->resultados[$alumno_id]['puntaje'] = number_format($puntaje, 3, '.', '');
        }
    }

    public function save()
    {
        if (auth()->user()->hasRole('alumno')) return;

        $this->validate();

        // Verificación de seguridad: Todas las filas deben sumar 100 exactamente
        foreach ($this->resultados as $user_id => $data) {
            $suma = (int)$data['correctas'] + (int)$data['incorrectas'] + (int)$data['blanco'];
            if ($suma !== 100) {
                session()->flash('error', "No se puede guardar. El estudiante {$data['nombre']} tiene $suma preguntas (deben ser 100).");
                return;
            }
        }

        try {
            DB::transaction(function () {
                foreach ($this->resultados as $user_id => $data) {
                    ResultadoSimulacro::updateOrCreate(
                        [
                            'simulacro_id' => $this->simulacro_id, 
                            'alumno_id' => $user_id
                        ],
                        [
                            'correctas' => (int)$data['correctas'],
                            'incorrectas' => (int)$data['incorrectas'],
                            'blanco' => (int)$data['blanco'],
                            'puntaje' => $data['puntaje'],
                        ]
                    );
                }
                $this->recalcularRanking();
            });

            session()->flash('message', 'Puntajes guardados con éxito.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar: ' . $e->getMessage());
        }
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

    public function cargarNotasAlumno()
    {
        $this->misResultados = ResultadoSimulacro::with(['simulacro.area', 'simulacro.ciclo'])
            ->where('alumno_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.c-r-u-d.puntajesimulacro', [
            'areas' => Area::all(),
            'ciclos' => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect(),
            'simulacros' => $this->ciclo_id ? Simulacro::where('ciclo_id', $this->ciclo_id)->get() : collect()
        ]);
    }
}