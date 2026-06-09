<?php

namespace App\Livewire\CRUD;

use App\Models\Carrera as CarreraModel;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class Carrera extends Component
{
    use WithPagination;

    // Cambiamos isOpen por view para manejar la navegación por estados
    public $view = 'index'; 
    public $nombre, $area_id, $carrera_id;
    public $search = '';

    protected function rules()
    {
        return [
            'nombre' => 'required|min:3|unique:carreras,nombre,' . $this->carrera_id,
            'area_id' => 'required|exists:areas,id',
        ];
    }

    public function render()
    {
        return view('livewire.c-r-u-d.carrera', [
            'carreras' => CarreraModel::with('area')
                ->where('nombre', 'like', '%' . $this->search . '%')
                ->orWhereHas('area', function($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate(10),
            'areas' => Area::all()
        ]);
    }

    // Heurística #3: Control y libertad del usuario
    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function volver()
    {
        $this->view = 'index';
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['nombre', 'area_id', 'carrera_id']);
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        CarreraModel::updateOrCreate(['id' => $this->carrera_id], [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
        ]);

        session()->flash('message', $this->carrera_id ? 'Carrera actualizada con éxito.' : 'Carrera registrada con éxito.');
        
        // Al terminar, volvemos al listado (Visibilidad del estado del sistema)
        $this->volver();
    }

    public function edit($id)
    {
        $carrera = CarreraModel::findOrFail($id);
        $this->carrera_id = $id;
        $this->nombre = $carrera->nombre;
        $this->area_id = $carrera->area_id;
        $this->view = 'edit';
    }

    public function delete($id)
    {
        CarreraModel::find($id)->delete();
        session()->flash('message', 'Carrera eliminada del sistema.');
    }
}