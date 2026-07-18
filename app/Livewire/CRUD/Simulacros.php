<?php

namespace App\Livewire\CRUD;

use App\Models\Simulacro;
use App\Models\Area;
use App\Models\Ciclo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Simulacros extends Component
{
    use WithPagination;

    // Estado de la vista: 'index', 'create', 'edit'
    #[Url]
    public $view = 'index';

    // Propiedades del formulario
    public $nombre, $area_id, $ciclo_id, $fecha, $simulacro_id;
    public $puntaje_maximo = 400; // Valor por defecto
    
    public $search = '';

    // Propiedades para Modal de Eliminación
    public $confirmandoEliminacion = false;
    public $simulacro_id_eliminar;

    // --- REGLAS DE VALIDACIÓN ---
    protected function rules()
    {
        return [
            'nombre'   => 'required|min:5|string',
            'area_id'  => 'required|exists:areas,id',
            'ciclo_id' => 'required|exists:ciclos,id',
            'fecha'    => 'required|date',
        ];
    }

    // --- MENSAJES EN ESPAÑOL ---
    protected $messages = [
        'nombre.required'   => 'El nombre del simulacro es obligatorio.',
        'nombre.min'        => 'El nombre debe tener al menos 5 caracteres.',
        'nombre.string'     => 'El nombre debe ser una cadena de texto válida.',
        'area_id.required'  => 'Debe seleccionar un área académica.',
        'area_id.exists'    => 'El área seleccionada no es válida.',
        'ciclo_id.required' => 'Debe seleccionar un ciclo académico.',
        'ciclo_id.exists'   => 'El ciclo seleccionado no es válido.',
        'fecha.required'    => 'La fecha del simulacro es obligatoria.',
        'fecha.date'        => 'El formato de la fecha no es válido.',
    ];

    public function updatedAreaId($value)
    {
        $this->ciclo_id = '';
    }

    // --- Navegación ---
    
    public function showIndex()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    public function showCreate()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function showEdit($id)
    {
        $this->resetInputFields();
        $simulacro = Simulacro::findOrFail($id);
        $this->simulacro_id = $id;
        $this->nombre = $simulacro->nombre;
        $this->area_id = $simulacro->area_id;
        $this->ciclo_id = $simulacro->ciclo_id;
        $this->fecha = $simulacro->fecha->format('Y-m-d');
        
        $this->view = 'edit';
    }

    private function resetInputFields()
    {
        $this->reset(['nombre', 'area_id', 'ciclo_id', 'fecha', 'simulacro_id', 'confirmandoEliminacion', 'simulacro_id_eliminar']);
        $this->puntaje_maximo = 400; 
        $this->resetValidation();
    }

    // --- Gestión de Eliminación ---

    public function abrirConfirmacionEliminacion($id)
    {
        $this->simulacro_id_eliminar = $id;
        $this->confirmandoEliminacion = true;
    }

    public function cerrarConfirmacionEliminacion()
    {
        $this->confirmandoEliminacion = false;
        $this->simulacro_id_eliminar = null;
    }

    public function delete()
    {
        Simulacro::findOrFail($this->simulacro_id_eliminar)->delete();
        
        $this->cerrarConfirmacionEliminacion();
        session()->flash('message', 'Simulacro eliminado correctamente.');
    }

    // --- Acciones de Guardado ---

    public function store()
    {
        $this->validate();

        Simulacro::updateOrCreate(['id' => $this->simulacro_id], [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
            'ciclo_id' => $this->ciclo_id,
            'fecha' => $this->fecha,
            'puntaje_maximo' => 400, 
        ]);

        session()->flash('message', $this->simulacro_id ? 'Simulacro actualizado correctamente.' : 'Simulacro creado con éxito.');
        $this->showIndex();
    }

    public function render()
    {
        return view('livewire.c-r-u-d.simulacros', [
            'simulacros' => Simulacro::with(['area', 'ciclo'])
                ->where('nombre', 'like', '%' . $this->search . '%')
                ->orderBy('fecha', 'desc')
                ->paginate(10),
            'areas' => Area::all(),
            'ciclos' => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect()
        ]);
    }
}