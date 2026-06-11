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

    // Control de vista: 'index', 'create', 'edit', 'show'
    public $view = 'index';

    // Propiedades del formulario
    public $nombre, $area_id, $ciclo_id, $curso_id;
    
    // Propiedad para almacenar el curso seleccionado en la vista de detalle
    public $selectedCurso;
    
    // Propiedades de búsqueda
    public $search = '';

    protected $rules = [
        'nombre' => 'required|min:3|string',
        'area_id' => 'required|exists:areas,id',
        'ciclo_id' => 'required|exists:ciclos,id',
    ];

    /**
     * Resetear ciclo_id cuando cambie el area_id para mantener integridad
     */
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
            // Filtrar ciclos según el área seleccionada en el formulario
            'ciclos' => Ciclo::where('area_id', $this->area_id)
                        ->where('activo', true)
                        ->get()
        ]);
    }

    // --- Navegación y Detalles ---

    /**
     * Carga un curso específico con sus relaciones (incluyendo docentes)
     */
    public function show($id)
    {
        // Cargamos el curso con su área, su ciclo y la lista de docentes con sus datos de identidad (user)
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

    /**
     * Método para limpiar estado y volver al listado principal
     */
    public function volver()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields() {
        $this->reset(['nombre', 'area_id', 'ciclo_id', 'curso_id', 'selectedCurso']);
        $this->resetValidation();
    }

    // --- Acciones de persistencia ---

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

    public function delete($id)
    {
        CursoModel::find($id)->delete();
        session()->flash('message', 'Curso eliminado.');
    }
}