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
    public $nombre, $area_id, $ciclo_id, $fecha, $puntaje_maximo = 1000, $simulacro_id;
    
    public $search = '';

    protected function rules()
    {
        return [
            'nombre' => 'required|min:5|string',
            'area_id' => 'required|exists:areas,id',
            'ciclo_id' => 'required|exists:ciclos,id',
            'fecha' => 'required|date',
            'puntaje_maximo' => 'required|numeric|min:0',
        ];
    }

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
        $this->puntaje_maximo = $simulacro->puntaje_maximo;
        
        $this->view = 'edit';
    }

    private function resetInputFields()
    {
        $this->reset(['nombre', 'area_id', 'ciclo_id', 'fecha', 'puntaje_maximo', 'simulacro_id']);
        $this->resetValidation();
    }

    // --- Acciones ---

    public function store()
    {
        $this->validate();

        Simulacro::updateOrCreate(['id' => $this->simulacro_id], [
            'nombre' => $this->nombre,
            'area_id' => $this->area_id,
            'ciclo_id' => $this->ciclo_id,
            'fecha' => $this->fecha,
            'puntaje_maximo' => $this->puntaje_maximo,
        ]);

        session()->flash('message', $this->simulacro_id ? 'Simulacro actualizado.' : 'Simulacro creado.');
        $this->showIndex();
    }

    public function delete($id)
    {
        Simulacro::find($id)->delete();
        session()->flash('message', 'Simulacro eliminado.');
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