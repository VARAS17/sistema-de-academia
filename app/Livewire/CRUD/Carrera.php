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
    
    public $selectedCarrera;

    // NUEVO: Propiedad para controlar el modal de eliminación
    public $carreraIdBeingDeleted = null;

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

    public function show($id)
    {
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

    public function edit($id)
    {
        $carrera = CarreraModel::findOrFail($id);
        $this->carrera_id = $id;
        $this->nombre = $carrera->nombre;
        $this->area_id = $carrera->area_id;
        $this->view = 'edit';
    }

    public function volver()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields()
    {
        // ACTUALIZADO: Resetear también el ID de eliminación
        $this->reset(['nombre', 'area_id', 'carrera_id', 'selectedCarrera', 'carreraIdBeingDeleted']);
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

    // --- NUEVOS MÉTODOS PARA EL MODAL DE ELIMINACIÓN ---

    /**
     * Guarda el ID de la carrera y activa la visualización del modal en el front
     */
    public function confirmDelete($id)
    {
        $this->carreraIdBeingDeleted = $id;
    }

    /**
     * Limpia el ID y cierra el modal
     */
    public function cancelDelete()
    {
        $this->carreraIdBeingDeleted = null;
    }

    /**
     * Ejecuta la eliminación definitiva (sin parámetros)
     */
    public function delete()
    {
        if ($this->carreraIdBeingDeleted) {
            $carrera = CarreraModel::findOrFail($this->carreraIdBeingDeleted);

            // Validamos si la carrera tiene alumnos asociados
            if ($carrera->alumnos()->exists()) {
                session()->flash('error', 'No se puede eliminar la carrera "' . $carrera->nombre . '" porque tiene alumnos inscritos. Reasigne a los alumnos antes de continuar.');
            } else {
                $carrera->delete();
                session()->flash('message', 'Carrera eliminada del sistema.');
            }

            $this->carreraIdBeingDeleted = null;
        }
    }
}