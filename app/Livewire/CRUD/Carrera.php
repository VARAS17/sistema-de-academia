<?php

namespace App\Livewire\CRUD;

use App\Models\Carrera as CarreraModel;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class Carrera extends Component
{
    use WithPagination;

    public $view = 'index'; 
    public $nombre, $area_id, $carrera_id;
    public $search = '';
    
    // Propiedad para la vista de detalle
    public $selectedCarrera;

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

    // --- NUEVO: Método para ver detalles ---
    public function show($id)
    {
        // Cargamos la carrera con su área, los ciclos de esa área y los alumnos con su identidad
        $this->selectedCarrera = CarreraModel::with([
            'area', 
            'ciclos', 
            'alumnos.user', 
            'alumnos.ciclo'
        ])->findOrFail($id);
        
        $this->view = 'show';
    }

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function volver()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields()
    {
        $this->reset(['nombre', 'area_id', 'carrera_id', 'selectedCarrera']);
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