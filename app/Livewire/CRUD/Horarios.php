<?php

namespace App\Livewire\CRUD;

use App\Models\Area;
use App\Models\Ciclo;
use App\Models\Horario;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Horarios extends Component
{
    use WithFileUploads, AuthorizesRequests;

    // Propiedades de datos
    public $nombre, $area_id, $ciclo_id, $imagen, $horario_id;
    public $filtro_area, $filtro_ciclo;
    
    public $areas = [];
    public $ciclos = []; 
    public $ciclos_filtro = [];

    // Propiedades para Modal de Eliminación
    public $confirmandoEliminacion = false;
    public $horario_id_eliminar;

    // Control de navegación
    public $view = 'index'; 

    public function mount()
    {
        $this->areas = Area::all();
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            $perfil = $user->alumno; 
            if ($perfil && $perfil->carrera) {
                $this->filtro_area = $perfil->carrera->area_id; 
                $this->filtro_ciclo = $perfil->ciclo_id;
            }
        }
    }

    public function updatedAreaId($value)
    {
        $this->ciclos = Ciclo::where('area_id', $value)->get();
        $this->ciclo_id = null;
    }

    public function updatedFiltroArea($value)
    {
        $this->ciclos_filtro = Ciclo::where('area_id', $value)->get();
        $this->filtro_ciclo = null;
    }

    // --- Métodos de Confirmación de Eliminación ---

    public function abrirConfirmacionEliminacion($id)
    {
        if (!Auth::user()->hasRole('admin')) return;
        $this->horario_id_eliminar = $id;
        $this->confirmandoEliminacion = true;
    }

    public function cerrarConfirmacionEliminacion()
    {
        $this->confirmandoEliminacion = false;
        $this->horario_id_eliminar = null;
    }

    public function delete()
    {
        if (!Auth::user()->hasRole('admin')) return;

        $horario = Horario::findOrFail($this->horario_id_eliminar);
        
        if($horario->imagen) {
            Storage::disk('public')->delete($horario->imagen);
        }
        
        $horario->delete();
        
        $this->cerrarConfirmacionEliminacion();
        session()->flash('message', 'Horario eliminado correctamente.');
    }

    // --- Gestión de Breadcrumbs dinámicos ---

    private function getBreadcrumbs()
    {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('dashboard')],
            ['name' => 'Horarios', 'url' => $this->view !== 'index' ? '#' : null],
        ];

        if ($this->view === 'create') {
            $breadcrumbs[] = ['name' => 'Nuevo Horario', 'url' => null];
        } elseif ($this->view === 'edit') {
            $breadcrumbs[] = ['name' => 'Editar Horario', 'url' => null];
        }

        return $breadcrumbs;
    }

    // --- Métodos de Navegación y CRUD ---

    public function create()
    {
        if (!Auth::user()->hasRole('admin')) return;
        $this->resetFields();
        $this->view = 'create';
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole('admin')) return;
        
        $horario = Horario::findOrFail($id);
        $this->horario_id = $id;
        $this->nombre = $horario->nombre;
        $this->area_id = $horario->area_id;
        $this->ciclos = Ciclo::where('area_id', $horario->area_id)->get();
        $this->ciclo_id = $horario->ciclo_id;
        
        $this->view = 'edit';
    }

    public function cancel()
    {
        $this->resetFields();
        $this->view = 'index';
    }

    public function save()
    {
        if (!Auth::user()->hasRole('admin')) abort(403);

        $this->validate([
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'ciclo_id' => 'required|exists:ciclos,id',
            'imagen' => $this->horario_id ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $data = [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
            'ciclo_id' => $this->ciclo_id,
        ];

        if ($this->imagen) {
            if ($this->horario_id) {
                $old = Horario::find($this->horario_id);
                if($old && $old->imagen) Storage::disk('public')->delete($old->imagen);
            }
            $data['imagen'] = $this->imagen->store('horarios', 'public');
        }

        Horario::updateOrCreate(['id' => $this->horario_id], $data);

        session()->flash('message', 'Operación exitosa.');
        $this->cancel();
    }

    public function resetFields() 
    {
        $this->nombre = ''; 
        $this->area_id = ''; 
        $this->ciclo_id = '';
        $this->imagen = null; 
        $this->horario_id = null; 
        $this->ciclos = [];
        $this->confirmandoEliminacion = false;
        $this->horario_id_eliminar = null;
    }

    public function render()
    {
        $user = Auth::user();
        $query = Horario::query()->with(['area', 'ciclo']);

        if ($user->hasRole('admin')) {
            if ($this->filtro_area) $query->where('area_id', $this->filtro_area);
            if ($this->filtro_ciclo) $query->where('ciclo_id', $this->filtro_ciclo);
        } else {
            $perfil = $user->alumno;
            if ($perfil && $perfil->carrera && $perfil->ciclo_id) {
                $query->where('area_id', $perfil->carrera->area_id)
                      ->where('ciclo_id', $perfil->ciclo_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return view('livewire.c-r-u-d.horarios', [
            'horarios' => $query->latest()->get(),
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}