<?php

namespace App\Livewire\CRUD;

use App\Models\PagoMatricula;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GestionPagos extends Component
{
    use WithPagination, WithFileUploads;

    // Control de Vistas: index, detalle, subir_voucher
    public $view = 'index';

    // Propiedades del Formulario
    public $pago_id, $fecha_pago, $evidencia, $estado = 'Pagado';
    
    // Propiedad para Ver Detalle
    public $pagoSeleccionado;

    public $search = '';

    protected function rules()
    {
        return [
            'fecha_pago' => 'required|date',
            'evidencia' => 'required|image|max:2048',
            'estado' => 'required|in:Pagado,Observado',
        ];
    }

    public function render()
    {
        $query = PagoMatricula::with(['matricula.alumno.user', 'matricula.ciclo', 'matricula.alumno.carrera']);

        // FILTRO POR ROL: Si es alumno, solo ve sus propios pagos
        if (auth()->user()->hasRole('alumno')) {
            $query->whereHas('matricula.alumno', function($q) {
                $q->where('user_id', auth()->id());
            });
        } else {
            // Filtro Admin
            if ($this->search) {
                $query->where(function($q) {
                    $q->whereHas('matricula.alumno.user', function($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('matricula.alumno', function($alumnoQuery) {
                        $alumnoQuery->where('dni', 'like', '%' . $this->search . '%');
                    });
                });
            }
        }

        return view('livewire.c-r-u-d.gestion-pagos', [
            'pagos' => $query->orderBy('estado', 'asc')
                ->orderBy('fecha_vencimiento', 'asc')
                ->paginate(15)
        ]);
    }

    // --- Navegación ---

    public function showIndex()
    {
        $this->reset(['pago_id', 'fecha_pago', 'evidencia', 'pagoSeleccionado']);
        $this->resetValidation();
        $this->view = 'index';
    }

    public function verDetalle($id)
    {
        // Se corrigió la relación a matricula.alumno.carrera
        $this->pagoSeleccionado = PagoMatricula::with([
            'matricula.alumno.user', 
            'matricula.ciclo', 
            'matricula.alumno.carrera'
        ])->findOrFail($id);
        
        $this->view = 'detalle';
    }

    public function registrarPago($id)
    {
        if (auth()->user()->hasRole('alumno')) return;

        $this->pago_id = $id;
        $this->pagoSeleccionado = PagoMatricula::with(['matricula.alumno.user'])->findOrFail($id);
        $this->fecha_pago = now()->format('Y-m-d');
        $this->view = 'subir_voucher';
    }

    // --- Acciones ---

    public function save()
    {
        if (auth()->user()->hasRole('alumno')) return;

        $this->validate();

        $pago = PagoMatricula::findOrFail($this->pago_id);
        
        // Guardar evidencia
        $path = $this->evidencia->store('vouchers', 'public');

        $pago->update([
            'fecha_pago' => $this->fecha_pago,
            'evidencia' => $path,
            'estado' => $this->estado
        ]);

        session()->flash('message', 'Pago verificado correctamente.');
        $this->showIndex();
    }

    public function exportarPDF($id)
    {
        $pago = PagoMatricula::with([
            'matricula.alumno.user', 
            'matricula.ciclo', 
            'matricula.alumno.carrera'
        ])->findOrFail($id);

        // Validación de seguridad
        if (auth()->user()->hasRole('alumno') && $pago->matricula->alumno->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este documento.');
        }

        // --- LÓGICA PARA DETERMINAR EL NÚMERO DE CUOTA ---
        // Contamos cuántos registros de pago existen para esta matrícula 
        // cuyo ID sea menor o igual al actual (orden cronológico)
        $numeroCuota = PagoMatricula::where('matricula_id', $pago->matricula_id)
            ->where('id', '<=', $pago->id)
            ->count();

        $nombresCuotas = [
            1 => 'Primera Cuota',
            2 => 'Segunda Cuota',
            3 => 'Tercera Cuota',
            4 => 'Cuarta Cuota',
            5 => 'Quinta Cuota',
            6 => 'Sexta Cuota',
        ];

        $concepto_dinamico = $nombresCuotas[$numeroCuota] ?? "Cuota N° {$numeroCuota}";
        // ------------------------------------------------

        $data = [
            'pago' => $pago,
            'concepto_texto' => $concepto_dinamico, // Pasamos la variable a la vista
            'fecha_emision' => now()->format('d/m/Y H:i A')
        ];

        $pdf = Pdf::loadView('reports.boleta-pago', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Boleta_Pago_{$pago->id}.pdf");
    }
}