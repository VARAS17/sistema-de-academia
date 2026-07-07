<?php

namespace App\Livewire\CRUD;

use App\Models\Area;
use App\Models\Simulacro;
use App\Models\Alumno;
use App\Models\ResultadoSimulacro;
use App\Models\Matricula; // Asegúrate de que este modelo exista
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Puntajesimulacro extends Component
{
    // Propiedades para Admin/Docente
    public $area_id, $simulacro_id;
    public $resultados = []; // Matriz de entrada

    // Propiedad para Alumno
    public $misResultados = [];

    protected $rules = [
        'simulacro_id' => 'required|exists:simulacros,id',
        'resultados.*.correctas' => 'required|integer|min:0',
        'resultados.*.incorrectas' => 'required|integer|min:0',
        'resultados.*.blanco' => 'required|integer|min:0',
        'resultados.*.puntaje' => 'required|numeric',
    ];

    public function mount()
    {
        // Si es alumno, cargamos sus notas inmediatamente
        if (auth()->user()->hasRole('alumno')) {
            $this->cargarNotasAlumno();
        }
    }

    public function updatedAreaId()
    {
        $this->simulacro_id = null;
        $this->resultados = [];
    }

    public function updatedSimulacroId()
    {
        if (!auth()->user()->hasRole('alumno')) {
            $this->cargarAlumnosParaEdicion();
        }
    }

    // Lógica para ADMIN: Cargar lista de alumnos para poner notas
    public function cargarAlumnosParaEdicion()
    {
        if (!$this->simulacro_id) return;

        $simulacro = Simulacro::findOrFail($this->simulacro_id);
        
        // CORRECCIÓN: Buscamos alumnos que tengan matrícula ACTIVA en el ciclo del simulacro
        $alumnos = Alumno::with('user')
            ->whereHas('matriculas', function($q) use ($simulacro) {
                $q->where('ciclo_id', $simulacro->ciclo_id)
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
                'correctas' => $existente->correctas ?? 0,
                'incorrectas' => $existente->incorrectas ?? 0,
                'blanco' => $existente->blanco ?? 0,
                'puntaje' => $existente->puntaje ?? 0,
            ];
        }
    }

    // Lógica para ALUMNO: Ver solo sus notas
    public function cargarNotasAlumno()
    {
        $this->misResultados = ResultadoSimulacro::with(['simulacro.area'])
            ->where('alumno_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function calcular($alumno_id)
    {
        $c = $this->resultados[$alumno_id]['correctas'];
        $i = $this->resultados[$alumno_id]['incorrectas'];
        
        // Fórmula de puntaje (puedes ajustarla)
        $this->resultados[$alumno_id]['puntaje'] = ($c * 4.025) - ($i * 0.975);
    }

    public function save()
    {
        if (auth()->user()->hasRole('alumno')) return;

        $this->validate();

        try {
            DB::transaction(function () {
                foreach ($this->resultados as $alumno_id => $data) {
                    ResultadoSimulacro::updateOrCreate(
                        ['simulacro_id' => $this->simulacro_id, 'alumno_id' => $alumno_id],
                        [
                            'correctas' => $data['correctas'],
                            'incorrectas' => $data['incorrectas'],
                            'blanco' => $data['blanco'],
                            'puntaje' => $data['puntaje'],
                        ]
                    );
                }
                $this->recalcularRanking();
            });

            session()->flash('message', 'Notas procesadas y ranking actualizado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    private function recalcularRanking()
    {
        $resultados = ResultadoSimulacro::where('simulacro_id', $this->simulacro_id)
            ->orderBy('puntaje', 'desc')
            ->get();

        foreach ($resultados as $puesto => $res) {
            $res->update(['puesto' => $puesto + 1]);
        }
    }

    public function render()
    {
        return view('livewire.c-r-u-d.puntajesimulacro', [
            'areas' => Area::all(),
            'simulacros' => $this->area_id 
                ? Simulacro::where('area_id', $this->area_id)->get() 
                : collect()
        ]);
    }
}