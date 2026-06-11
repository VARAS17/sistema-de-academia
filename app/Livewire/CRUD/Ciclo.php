<?php

namespace App\Livewire\CRUD;

use App\Models\Ciclo as CicloModel;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class Ciclo extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre, $area_id, $aula, $activo = true, $ciclo_id;
    
    // Propiedad para la vista de detalles
    public $selectedCiclo; 
    
    public $search = '';
    
    // Control de vistas (index, create, edit, show)
    public $view = 'index'; 

    protected function rules()
    {
        return [
            'nombre' => 'required|min:3|string',
            'area_id' => 'required|exists:areas,id',
            'aula' => 'required|string',
            'activo' => 'boolean',
        ];
    }

    public function render()
    {
        return view('livewire.c-r-u-d.ciclo', [
            'ciclos' => CicloModel::with('area')
                        ->where(function($query) {
                            $query->where('nombre', 'like', '%' . $this->search . '%')
                                  ->orWhereHas('area', function($q) {
                                      $q->where('nombre', 'like', '%' . $this->search . '%');
                                  });
                        })
                        ->orderBy('id', 'desc')
                        ->paginate(10),
            'areas' => Area::all()
        ]);
    }

    // NUEVO: Función para ver detalles y profesores
    public function show($id)
    {
        $this->selectedCiclo = CicloModel::with(['area', 'cursos.docentes.user'])->findOrFail($id);
        $this->view = 'show';
    }

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function edit($id)
    {
        $ciclo = CicloModel::findOrFail($id);
        $this->ciclo_id = $id;
        $this->nombre = $ciclo->nombre;
        $this->area_id = $ciclo->area_id;
        $this->aula = $ciclo->aula;
        $this->activo = $ciclo->activo;

        $this->view = 'edit';
    }

    public function volver()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields() {
        $this->nombre = '';
        $this->area_id = '';
        $this->aula = '';
        $this->activo = true;
        $this->ciclo_id = null;
        $this->selectedCiclo = null; // Limpiamos el ciclo seleccionado
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        CicloModel::updateOrCreate(['id' => $this->ciclo_id], [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
            'aula' => $this->aula,
            'activo' => $this->activo,
        ]);

        session()->flash('message', $this->ciclo_id ? 'Ciclo actualizado.' : 'Ciclo creado.');

        $this->volver();
    }

    public function delete($id)
    {
        CicloModel::find($id)->delete();
        session()->flash('message', 'Ciclo eliminado.');
    }
}