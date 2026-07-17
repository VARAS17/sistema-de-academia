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
    
    // Propiedades para Admin/Docente (Matriz de entrada)
    public $resultados = []; 

    // Propiedad para Alumno
    public $misResultados = [];

    /**
     * Reglas de validación: 
     * 'integer' asegura que no se acepten decimales en las entradas.
     * 'min:0' y 'max:100' para rangos lógicos.
     */
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
        // Si el usuario es alumno, cargamos su historial automáticamente
        if (auth()->user()->hasRole('alumno')) {
            $this->cargarNotasAlumno();
        }
    }

    // --- LÓGICA DE FILTROS EN CASCADA ---

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

    // --- CARGA DE DATOS PARA ADMIN ---

    public function cargarAlumnosParaEdicion()
    {
        if (!$this->simulacro_id || !$this->ciclo_id) {
            $this->resultados = [];
            return;
        }

        // Buscamos alumnos con matrícula ACTIVA en el ciclo seleccionado
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
                'error_suma' => false, // Bandera visual para el front
            ];
        }
    }

    // --- CÁLCULOS Y PROCESAMIENTO ---

    /**
     * Se activa vía wire:change o wire:input cuando el usuario digita valores
     */
    public function calcular($alumno_id)
    {
        // 1. Forzar conversión a entero (elimina decimales si el usuario intenta pegarlos)
        $c = intval($this->resultados[$alumno_id]['correctas'] ?? 0);
        $i = intval($this->resultados[$alumno_id]['incorrectas'] ?? 0);
        $b = intval($this->resultados[$alumno_id]['blanco'] ?? 0);
        
        // 2. Sincronizar de vuelta al array para que el front vea el número limpio
        $this->resultados[$alumno_id]['correctas'] = $c;
        $this->resultados[$alumno_id]['incorrectas'] = $i;
        $this->resultados[$alumno_id]['blanco'] = $b;

        $totalPreguntas = $c + $i + $b;

        // 3. Validar que la suma no exceda 100
        if ($totalPreguntas > 100) {
            $this->resultados[$alumno_id]['error_suma'] = true;
            $this->resultados[$alumno_id]['puntaje'] = 0;
            session()->flash("error_row_$alumno_id", "Exceso: $totalPreguntas preguntas.");
        } else {
            $this->resultados[$alumno_id]['error_suma'] = false;
            
            // Fórmula: (Correctas * 4.025) - (Incorrectas * 0.975)
            $puntaje = ($c * 4.025) - ($i * 0.975);
            
            // Guardamos el puntaje con 3 decimales pero no permitimos negativos
            $this->resultados[$alumno_id]['puntaje'] = number_format(max(0, $puntaje), 3, '.', '');
        }
    }

    public function save()
    {
        if (auth()->user()->hasRole('alumno')) return;

        // Validar tipos de datos básicos (enteros)
        $this->validate();

        // Validar integridad: que ninguna fila sume más de 100
        foreach ($this->resultados as $user_id => $data) {
            $sumaTotal = (int)$data['correctas'] + (int)$data['incorrectas'] + (int)$data['blanco'];
            if ($sumaTotal > 100) {
                session()->flash('error', "No se puede guardar. El alumno {$data['nombre']} tiene $sumaTotal preguntas.");
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

            session()->flash('message', 'Resultados guardados y ranking actualizado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error en el servidor: ' . $e->getMessage());
        }
    }

    private function recalcularRanking()
    {
        // Obtener resultados de este simulacro específico ordenados por puntaje
        $resultados = ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
            ->orderBy('puntaje', 'desc')
            ->get();

        foreach ($resultados as $indice => $res) {
            $res->update(['puesto' => $indice + 1]);
        }
    }

    // --- VISTA ALUMNO ---

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
            'ciclos' => $this->area_id 
                ? Ciclo::where('area_id', $this->area_id)->get() 
                : collect(),
            'simulacros' => $this->ciclo_id 
                ? Simulacro::where('ciclo_id', $this->ciclo_id)->get() 
                : collect()
        ]);
    }
}