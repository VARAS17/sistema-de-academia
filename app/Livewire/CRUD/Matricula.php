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

    // Propiedades de control y búsqueda
    public $matricula_id; 
    public $search = '';
    public $alumno_id; // Almacena el user_id del alumno
    public $nombre_alumno;
    
    // Propiedades del formulario
    public $modalidad = 'Pago Unico';
    public $monto_total;
    public $ciclo_id;
    public $estado = 'Pendiente';
    public $cuotas = [];

    // Navegación
    public $view = 'index'; 
    public $viewingMatricula = null;

    protected function rules()
    {
        $rules = [
            'alumno_id' => 'required|exists:alumnos,user_id',
            'monto_total' => 'required|numeric|min:1',
            'modalidad' => 'required|in:Pago Unico,2 Cuotas,3 Cuotas',
            'estado' => 'required|in:Pendiente,Activa,Anulada',
            'cuotas.*.monto' => 'required|numeric',
            'cuotas.*.fecha_vencimiento' => 'required|date',
        ];

        // Solo exige voucher de la Cuota 1 si es un registro nuevo
        if ($this->view == 'create') {
            $rules['cuotas.1.evidencia'] = 'required|image|max:2048';
        }

        return $rules;
    }

    // --- LÓGICA DE REACTIVIDAD ---

    public function updatedSearch()
    {
        // Solo resetea si el usuario está borrando o cambiando el texto manualmente
        if ($this->search !== $this->nombre_alumno) {
            $this->alumno_id = null;
            $this->nombre_alumno = null;
        }
    }

    public function updatedMontoTotal()
    {
        $this->resetCuotas();
    }

    public function updatedModalidad()
    {
        $this->resetCuotas();
    }

    public function selectAlumno($id = null)
    {
        if (!$id) return;

        $alumno = Alumno::with(['user', 'ciclo'])->find($id);
        
        if ($alumno) {
            $this->alumno_id = $alumno->user_id;
            $this->nombre_alumno = $alumno->user->name;
            $this->search = $alumno->user->name;
            $this->ciclo_id = $alumno->ciclo_id;
            
            if ($this->view == 'create') {
                $this->resetCuotas();
            }
        }
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
            $montoIndividual = $this->monto_total ? round($this->monto_total / $num, 2) : 0;
            
            $this->cuotas[$i] = [
                'monto' => $montoIndividual > 0 ? $montoIndividual : '',
                'fecha_vencimiento' => now()->addMonths($i-1)->format('Y-m-d'),
                'evidencia' => null,
                'existente_evidencia' => null,
            ];
        }
    }

    // --- RENDERIZADO ---

    public function render()
    {
        return view('livewire.c-r-u-d.matricula', [
            'resultados' => (strlen($this->search) > 2 && !$this->alumno_id)
                ? Alumno::with('user')
                    ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('dni', 'like', "%{$this->search}%")
                    ->take(5)->get() 
                : [],
            'matriculas' => MatriculaModel::with(['alumno.user', 'ciclo', 'pagos'])
                ->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }

    // --- ACCIONES ---

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
        $this->resetCuotas();
    }

    public function edit($id = null)
    {
        if (!$id) return;
        $this->resetInputFields();
        $this->view = 'edit';
        $this->matricula_id = $id;

        $matricula = MatriculaModel::with(['alumno.user', 'pagos'])->findOrFail($id);
        
        $this->alumno_id = $matricula->alumno_id;
        $this->nombre_alumno = $matricula->alumno->user->name;
        $this->search = $this->nombre_alumno;
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

    public function show($id = null)
    {
        if (!$id) return;
        $this->viewingMatricula = MatriculaModel::with(['alumno.user', 'ciclo', 'pagos'])->findOrFail($id);
        $this->view = 'show';
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $matricula = MatriculaModel::updateOrCreate(
                ['id' => $this->matricula_id],
                [
                    'alumno_id' => $this->alumno_id,
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

                // Manejo de voucher
                if (isset($data['evidencia']) && $data['evidencia']) {
                    $pagoData['evidencia'] = $data['evidencia']->store('vouchers', 'public');
                    // Solo la cuota 1 se marca como pagada automáticamente al subir voucher
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

        session()->flash('message', $this->matricula_id ? 'Matrícula actualizada.' : 'Matrícula registrada exitosamente.');
        $this->closeModal();
    }

    public function delete($id = null)
    {
        if (!$id) return;
        $matricula = MatriculaModel::findOrFail($id);
        foreach($matricula->pagos as $pago) {
            if($pago->evidencia) Storage::disk('public')->delete($pago->evidencia);
        }
        $matricula->pagos()->delete();
        $matricula->delete();
        session()->flash('message', 'Matrícula eliminada.');
    }

    public function closeModal()
    {
        $this->view = 'index';
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['matricula_id', 'search', 'alumno_id', 'nombre_alumno', 'monto_total', 'modalidad', 'estado', 'cuotas', 'viewingMatricula']);
        $this->resetValidation();
    }
}