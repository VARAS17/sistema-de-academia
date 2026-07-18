<?php

namespace App\Livewire\CRUD;

use App\Models\Curso as CursoModel;
use App\Models\Ciclo;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class Curso extends Component
{
    use WithPagination;

    public $view = 'index';
    public $nombre, $area_id, $ciclo_id, $curso_id;
    public $selectedCurso;
    public $search = '';

    // Propiedad para rastrear el curso a eliminar
    public $cursoIdBeingDeleted = null;

    // Reglas de validación
    protected $rules = [
        'nombre' => 'required|min:3|string',
        'area_id' => 'required|exists:areas,id',
        'ciclo_id' => 'required|exists:ciclos,id',
    ];

    // MENSAJES PERSONALIZADOS EN ESPAÑOL
    protected $messages = [
        'nombre.required' => 'El nombre del curso es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        'nombre.string' => 'El nombre debe ser un texto válido.',
        'area_id.required' => 'Debe seleccionar un área.',
        'area_id.exists' => 'El área seleccionada no es válida.',
        'ciclo_id.required' => 'Debe seleccionar un ciclo.',
        'ciclo_id.exists' => 'El ciclo seleccionado no es válido.',
    ];

    // Resetear paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedAreaId($value)
    {
        $this->ciclo_id = '';
    }

    public function render()
    {
        return view('livewire.c-r-u-d.curso', [
            'cursos' => CursoModel::with(['ciclo', 'area'])
                        ->where('nombre', 'like', '%' . $this->search . '%')
                        ->orderBy('id', 'desc')
                        ->paginate(10),
            'areas' => Area::all(),
            'ciclos' => Ciclo::where('area_id', $this->area_id)
                        ->where('activo', true)
                        ->get()
        ]);
    }

    public function show($id)
    {
        $this->selectedCurso = CursoModel::with(['area', 'ciclo', 'docentes.user'])->findOrFail($id);
        $this->view = 'show';
    }

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function edit($id)
    {
        $this->resetValidation();
        $curso = CursoModel::findOrFail($id);
        
        $this->curso_id = $id;
        $this->nombre = $curso->nombre;
        $this->area_id = $curso->area_id;
        $this->ciclo_id = $curso->ciclo_id;

        $this->view = 'edit';
    }

    public function volver()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields() {
        $this->reset(['nombre', 'area_id', 'ciclo_id', 'curso_id', 'selectedCurso', 'cursoIdBeingDeleted']);
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        CursoModel::updateOrCreate(['id' => $this->curso_id], [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
            'ciclo_id' => $this->ciclo_id,
        ]);

        session()->flash('message', $this->curso_id ? 'Curso actualizado exitosamente.' : 'Curso creado correctamente.');

        $this->volver();
    }

    public function confirmDelete($id)
    {
        $this->cursoIdBeingDeleted = $id;
    }

    public function cancelDelete()
    {
        $this->cursoIdBeingDeleted = null;
    }

    public function delete()
    {
        if ($this->cursoIdBeingDeleted) {
            CursoModel::find($this->cursoIdBeingDeleted)->delete();
            session()->flash('message', 'Curso eliminado correctamente.');
            $this->cursoIdBeingDeleted = null;
        }
    }
}