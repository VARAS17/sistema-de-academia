<?php

namespace App\Livewire\CRUD;

use App\Models\Alumno;
use App\Models\Matricula as MatriculaModel;
use App\Models\PagoMatricula;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Matricula extends Component
{
    use WithPagination, WithFileUploads;

    // Propiedades del modelo
    public $matricula_id; 
    public $search = '';
    public $selectedAlumno = null;
    public $modalidad = 'Pago Unico';
    public $monto_total;
    public $ciclo_id;
    public $estado = 'Pendiente';
    public $cuotas = [];

    // CONTROL DE NAVEGACIÓN (Heurística #3: Libertad del usuario)
    public $view = 'index'; // Valores: index, create, edit, show
    public $viewingMatricula = null;

    protected function rules()
    {
        $rules = [
            'selectedAlumno' => 'required',
            'monto_total' => 'required|numeric',
            'modalidad' => 'required',
            'estado' => 'required|in:Pendiente,Activa,Anulada',
            'cuotas.*.monto' => 'required|numeric',
            'cuotas.*.fecha_vencimiento' => 'required|date',
        ];

        // Evidencia obligatoria solo si es creación nueva para la Cuota 1
        if ($this->view == 'create') {
            $rules['cuotas.1.evidencia'] = 'required|image|max:2048';
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->selectedAlumno = null;
    }

    public function selectAlumno($id)
    {
        $this->selectedAlumno = Alumno::with(['user', 'carrera', 'ciclo.area'])->findOrFail($id);
        $this->ciclo_id = $this->selectedAlumno->ciclo_id;
        $this->search = $this->selectedAlumno->user->name;
        
        if ($this->view == 'create') {
            $this->resetCuotas();
        }
    }

    public function updatedModalidad()
    {
        $this->resetCuotas();
    }

    private function resetCuotas()
    {
        $this->cuotas = [];
        $num = match($this->modalidad) {
            '2 Cuotas' => 2,
            '3 Cuotas' => 3,
            default => 1,
        };

        for ($i = 1; $i <= $num; $i++) {
            $this->cuotas[$i] = [
                'monto' => $this->monto_total ? ($this->monto_total / $num) : '',
                'fecha_vencimiento' => now()->addMonths($i-1)->format('Y-m-d'),
                'evidencia' => null,
                'existente_evidencia' => null,
            ];
        }
    }

    public function render()
    {
        return view('livewire.c-r-u-d.matricula', [
            'resultados' => strlen($this->search) > 2 && !$this->selectedAlumno
                ? Alumno::with('user')
                    ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('dni', 'like', "%{$this->search}%")
                    ->take(5)->get() 
                : [],
            'matriculas' => MatriculaModel::with(['alumno.user', 'ciclo', 'pagos'])
                ->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }

    // --- ACCIONES DE NAVEGACIÓN ---

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
        $this->resetCuotas();
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->view = 'edit';
        $this->matricula_id = $id;

        $matricula = MatriculaModel::with(['alumno.user', 'pagos'])->findOrFail($id);
        
        $this->selectedAlumno = $matricula->alumno;
        $this->search = $matricula->alumno->user->name;
        $this->ciclo_id = $matricula->ciclo_id;
        $this->monto_total = $matricula->monto_total;
        $this->modalidad = $matricula->modalidad;
        $this->estado = $matricula->estado;

        foreach ($matricula->pagos as $pago) {
            $this->cuotas[$pago->numero_cuota] = [
                'id' => $pago->id,
                'monto' => $pago->monto,
                'fecha_vencimiento' => Carbon::parse($pago->fecha_vencimiento)->format('Y-m-d'),
                'evidencia' => null,
                'existente_evidencia' => $pago->evidencia,
            ];
        }
    }

    public function show($id)
    {
        $this->viewingMatricula = MatriculaModel::with(['alumno.user', 'ciclo', 'pagos'])->findOrFail($id);
        $this->view = 'show';
    }

    public function closeModal() // Este es el que usa el breadcrumb y el botón cancelar
    {
        $this->view = 'index';
        $this->resetInputFields();
    }

    // --- LÓGICA DE PERSISTENCIA ---

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $matricula = MatriculaModel::updateOrCreate(
                ['id' => $this->matricula_id],
                [
                    'alumno_id' => $this->selectedAlumno->user_id,
                    'ciclo_id' => $this->ciclo_id,
                    'monto_total' => $this->monto_total,
                    'modalidad' => $this->modalidad,
                    'estado' => $this->estado
                ]
            );

            foreach ($this->cuotas as $index => $data) {
                $pagoData = [
                    'monto' => $data['monto'],
                    'fecha_vencimiento' => $data['fecha_vencimiento'],
                ];

                if (isset($data['evidencia']) && $data['evidencia']) {
                    $pagoData['evidencia'] = $data['evidencia']->store('vouchers', 'public');
                    if ($index == 1) {
                        $pagoData['fecha_pago'] = now();
                        $pagoData['estado'] = 'Pagado';
                    }
                }

                PagoMatricula::updateOrCreate(
                    ['matricula_id' => $matricula->id, 'numero_cuota' => $index],
                    $pagoData
                );
            }
        });

        session()->flash('message', $this->matricula_id ? 'Matrícula actualizada.' : 'Matrícula registrada.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $matricula = MatriculaModel::findOrFail($id);
        foreach($matricula->pagos as $pago) {
            if($pago->evidencia) Storage::disk('public')->delete($pago->evidencia);
        }
        $matricula->pagos()->delete();
        $matricula->delete();
        session()->flash('message', 'Matrícula eliminada.');
    }

    private function resetInputFields()
    {
        $this->reset(['matricula_id', 'search', 'selectedAlumno', 'monto_total', 'modalidad', 'estado', 'cuotas', 'viewingMatricula']);
        $this->resetValidation();
    }
}