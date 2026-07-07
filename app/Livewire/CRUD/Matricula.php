<?php

namespace App\Livewire\CRUD;

use App\Models\Alumno;
use App\Models\Ciclo;
use App\Models\Carrera;
use App\Models\Area;
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

    // Propiedades de navegación y búsqueda
    public string $view = 'index'; 
    public $search = '';
    public $viewingMatricula = null;
    public $matriculaIdBeingDeleted = null;

    // Propiedades del Alumno Seleccionado
    public $alumno_id; // PK de la tabla alumnos (user_id)
    public $nombre_alumno;

    // Propiedades del Formulario de Matrícula
    public $matricula_id = null;
    public $area_id;
    public $ciclo_id;
    public $carrera_id;
    public $modalidad = 'Pago Unico';
    public $monto_total;
    public $estado = 'Activa';
    public $cuotas = [];

    protected function rules()
    {
        return [
            'alumno_id'   => 'required|exists:alumnos,user_id',
            'area_id'     => 'required|exists:areas,id',
            'ciclo_id'    => 'required|exists:ciclos,id',
            'carrera_id'  => 'required|exists:carreras,id',
            'monto_total' => 'required|numeric|min:1',
            'modalidad'   => 'required|in:Pago Unico,2 Cuotas,3 Cuotas',
            'estado'      => 'required|in:Pendiente,Activa,Anulada',
            'cuotas.*.monto'             => 'required|numeric',
            'cuotas.*.fecha_vencimiento' => 'required|date',
            'cuotas.1.evidencia'         => $this->view === 'create' ? 'required|image|max:2048' : 'nullable',
        ];
    }

    // --- CICLO DE VIDA Y REACTIVIDAD ---

    public function updatedAreaId($value)
    {
        $this->ciclo_id = null;
        $this->carrera_id = null;
    }

    public function updatedMontoTotal()
    {
        $this->generarCuotas();
    }

    public function updatedModalidad()
    {
        $this->generarCuotas();
    }

    public function selectAlumno($id)
    {
        $alumno = Alumno::with('user')->find($id);
        if ($alumno) {
            $this->alumno_id = $alumno->user_id;
            $this->nombre_alumno = $alumno->user->name;
            $this->search = $alumno->user->name;
        }
    }

    private function generarCuotas()
    {
        $this->cuotas = [];
        $num = match($this->modalidad) {
            '2 Cuotas' => 2,
            '3 Cuotas' => 3,
            default    => 1,
        };

        $montoIndividual = $this->monto_total ? round($this->monto_total / $num, 2) : 0;

        for ($i = 1; $i <= $num; $i++) {
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
            'alumnos_busqueda' => (strlen($this->search) > 2 && !$this->alumno_id)
                ? Alumno::with('user')
                    ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('dni', 'like', "%{$this->search}%")
                    ->take(5)->get() 
                : [],
            'matriculas' => MatriculaModel::with(['alumno.user', 'ciclo', 'carrera', 'pagos'])
                ->orderByDesc('created_at')->paginate(10),
            'areas'    => Area::orderBy('nombre')->get(),
            'ciclos'   => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect(),
            'carreras' => $this->area_id ? Carrera::where('area_id', $this->area_id)->get() : collect(),
        ]);
    }

    // --- ACCIONES CRUD ---

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
        $this->generarCuotas();
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $matricula = MatriculaModel::with(['alumno.user', 'pagos', 'ciclo', 'carrera'])->findOrFail($id);
        
        $this->matricula_id  = $matricula->id;
        $this->alumno_id     = $matricula->alumno_id;
        $this->nombre_alumno = $matricula->alumno->user->name;
        $this->search        = $this->nombre_alumno;
        $this->area_id       = $matricula->ciclo->area_id;
        $this->ciclo_id      = $matricula->ciclo_id;
        $this->carrera_id    = $matricula->carrera_id;
        $this->monto_total   = $matricula->monto_total;
        $this->modalidad     = $matricula->modalidad;
        $this->estado        = $matricula->estado;

        foreach ($matricula->pagos as $pago) {
            $this->cuotas[$pago->numero_cuota] = [
                'id' => $pago->id,
                'monto' => $pago->monto,
                'fecha_vencimiento' => $pago->fecha_vencimiento,
                'evidencia' => null,
                'existente_evidencia' => $pago->evidencia,
            ];
        }
        $this->view = 'edit';
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // 1. Crear o Actualizar la Matrícula (Cabecera)
                $matricula = MatriculaModel::updateOrCreate(
                    ['id' => $this->matricula_id],
                    [
                        'alumno_id'   => $this->alumno_id,
                        'ciclo_id'    => $this->ciclo_id,
                        'carrera_id'  => $this->carrera_id,
                        'monto_total' => $this->monto_total,
                        'modalidad'   => $this->modalidad,
                        'estado'      => $this->estado
                    ]
                );

                // 2. Gestionar los Pagos (Detalle)
                foreach ($this->cuotas as $numero => $data) {
                    $pagoData = [
                        'monto' => $data['monto'],
                        'fecha_vencimiento' => $data['fecha_vencimiento'],
                    ];

                    // Subida de Voucher si existe
                    if (isset($data['evidencia']) && $data['evidencia']) {
                        $pagoData['evidencia'] = $data['evidencia']->store('vouchers', 'public');
                        $pagoData['fecha_pago'] = now();
                        $pagoData['estado'] = 'Pagado';
                    }

                    PagoMatricula::updateOrCreate(
                        ['matricula_id' => $matricula->id, 'numero_cuota' => $numero],
                        $pagoData
                    );
                }
            });

            session()->flash('message', $this->matricula_id ? 'Matrícula actualizada.' : 'Matrícula exitosa.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- ELIMINACIÓN ---

    public function confirmDelete($id)
    {
        $this->matriculaIdBeingDeleted = $id;
    }

    public function delete()
    {
        if ($this->matriculaIdBeingDeleted) {
            $matricula = MatriculaModel::with('pagos')->findOrFail($this->matriculaIdBeingDeleted);
            
            // Borrar imágenes de vouchers del storage
            foreach($matricula->pagos as $pago) {
                if($pago->evidencia) Storage::disk('public')->delete($pago->evidencia);
            }
            
            $matricula->delete(); // OnDelete Cascade debería limpiar pagos_matriculas
            
            session()->flash('message', 'Matrícula y registros de pago eliminados.');
            $this->matriculaIdBeingDeleted = null;
        }
    }

    // --- UTILIDADES ---

    public function show($id)
    {
        $this->viewingMatricula = MatriculaModel::with(['alumno.user', 'ciclo.area', 'carrera', 'pagos'])->findOrFail($id);
        $this->view = 'show';
    }

    public function closeModal()
    {
        $this->view = 'index';
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset([
            'matricula_id', 'search', 'alumno_id', 'nombre_alumno', 
            'area_id', 'ciclo_id', 'carrera_id', 'monto_total', 
            'modalidad', 'estado', 'cuotas', 'viewingMatricula'
        ]);
        $this->resetValidation();
    }
}