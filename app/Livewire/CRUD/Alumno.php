<?php

namespace App\Livewire\CRUD;

use App\Models\User;
use App\Models\Alumno as AlumnoModel;
use App\Models\Ciclo;
use App\Models\Carrera;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Alumno extends Component
{
    use WithPagination;

    public string $view = 'index';
    public $alumno_id = null; // Guardará el user_id
    public string $name = '', $email = '', $password = '', $dni = '', $telefono = '';
    public ?int $area_id = null, $ciclo_id = null, $carrera_id = null;
    public string $search = '';

    protected function rules(): array
    {
        return [
            'name'      => 'required|string|min:3|max:255',
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($this->alumno_id, 'id')],
            'password'  => $this->view === 'create' ? 'required|min:6' : 'nullable|min:6',
            'dni'       => ['required', 'digits:8', Rule::unique('alumnos', 'dni')->ignore($this->alumno_id, 'user_id')],
            'area_id'   => 'required|exists:areas,id',
            'ciclo_id'  => 'required|exists:ciclos,id',
            'carrera_id'=> 'required|exists:carreras,id',
        ];
    }

    public function updatedAreaId(): void {
        $this->ciclo_id = null;
        $this->carrera_id = null;
    }

    public function render()
    {
        return view('livewire.c-r-u-d.alumno', [
            'alumnos'  => AlumnoModel::with(['user', 'ciclo.area', 'carrera'])
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhere('dni', 'like', "%{$this->search}%")
                ->orderByDesc('created_at')->paginate(10),
            'areas'    => Area::orderBy('nombre')->get(),
            'ciclos'   => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect(),
            'carreras' => $this->area_id ? Carrera::where('area_id', $this->area_id)->get() : collect(),
        ]);
    }

    public function create() {
        $this->resetForm();
        $this->view = 'create';
    }

    public function store() {
        $this->validate();
        DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('alumno');
            AlumnoModel::create([
                'user_id' => $user->id,
                'dni' => $this->dni,
                'telefono' => $this->telefono ?: null,
                'ciclo_id' => $this->ciclo_id,
                'carrera_id' => $this->carrera_id,
            ]);
        });
        session()->flash('message', 'Alumno creado.');
        $this->showIndex();
    }

    public function edit($id) {
        $alumno = AlumnoModel::with('user', 'ciclo')->findOrFail($id);
        $this->alumno_id = $alumno->user_id;
        $this->name = $alumno->user->name;
        $this->email = $alumno->user->email;
        $this->dni = $alumno->dni;
        $this->telefono = $alumno->telefono;
        $this->area_id = $alumno->ciclo->area_id;
        $this->ciclo_id = $alumno->ciclo_id;
        $this->carrera_id = $alumno->carrera_id;
        $this->view = 'edit';
    }

    public function update() {
        $this->validate();
        $alumno = AlumnoModel::findOrFail($this->alumno_id);
        DB::transaction(function () use ($alumno) {
            $alumno->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : $alumno->user->password,
            ]);
            $alumno->update([
                'dni' => $this->dni,
                'telefono' => $this->telefono,
                'ciclo_id' => $this->ciclo_id,
                'carrera_id' => $this->carrera_id,
            ]);
        });
        session()->flash('message', 'Actualizado.');
        $this->showIndex();
    }

    public function delete($id) {
        DB::transaction(function () use ($id) {
            $alumno = AlumnoModel::findOrFail($id);
            $userId = $alumno->user_id;
            $alumno->delete();
            User::where('id', $userId)->delete();
        });
        session()->flash('message', 'Eliminado.');
    }

    public function showIndex() { $this->resetForm(); $this->view = 'index'; }
    private function resetForm() { $this->reset(['alumno_id','name','email','password','dni','telefono','area_id','ciclo_id','carrera_id']); }
}