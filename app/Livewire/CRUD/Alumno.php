<?php

namespace App\Livewire\CRUD;

use App\Models\User;
use App\Models\Alumno as AlumnoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Alumno extends Component
{
    use WithPagination;

    public string $view = 'index';
    
    // Propiedades del formulario (Solo identidad)
    public $alumno_id = null; // Este es el user_id
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $dni = null;
    public ?string $telefono = null;
    
    public string $search = '';
    public $selectedAlumno;

    // Control del modal de eliminación
    public $alumnoIdBeingDeleted = null;

    protected function rules(): array
    {
        return [
            'name'      => 'required|string|min:3|max:255',
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($this->alumno_id)],
            'password'  => $this->view === 'create' ? 'required|min:6' : 'nullable|min:6',
            'dni'       => ['required', 'digits:8', Rule::unique('alumnos', 'dni')->ignore($this->alumno_id, 'user_id')],
            'telefono'  => 'nullable|digits_between:7,15',
        ];
    }

    public function render()
    {
        return view('livewire.c-r-u-d.alumno', [
            'alumnos'  => AlumnoModel::with(['user', 'matriculas.ciclo']) // Cargamos matrículas para vista informativa
                ->where(function($query) {
                    $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                          ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orderByDesc('created_at')
                ->paginate(10),
        ]);
    }

    // --- NAVEGACIÓN ---

    public function show($id) {
        // Obtenemos el alumno con sus relaciones académicas solo para visualización
        $this->selectedAlumno = AlumnoModel::with(['user', 'matriculas.ciclo', 'matriculas.pagos'])->findOrFail($id);
        $this->view = 'show';
    }

    public function create() {
        $this->resetForm();
        $this->view = 'create';
    }

    public function edit($id) {
        $this->resetForm();
        $alumno = AlumnoModel::with('user')->findOrFail($id);
        
        $this->alumno_id = $alumno->user_id;
        $this->name      = $alumno->user->name;
        $this->email     = $alumno->user->email;
        $this->dni       = $alumno->dni;
        $this->telefono  = $alumno->telefono;
        $this->view      = 'edit';
    }

    public function cancel() { 
        $this->resetForm();
        $this->view = 'index'; 
    }

    // --- ACCIONES DE PERSISTENCIA (SÓLO IDENTIDAD) ---

    public function store() {
        $this->validate();

        DB::transaction(function () {
            // 1. Crear el usuario de acceso
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole('alumno');

            // 2. Crear la ficha del alumno
            AlumnoModel::create([
                'user_id' => $user->id,
                'dni' => $this->dni,
                'telefono' => $this->telefono ?: null,
                // Nota: ciclo_id y carrera_id ya no se guardan aquí, se hacen en Matrícula
            ]);
        });

        session()->flash('message', 'Alumno creado correctamente. Ahora puede proceder a la matrícula.');
        $this->cancel();
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
            ]);
        });

        session()->flash('message', 'Datos personales actualizados.');
        $this->cancel();
    }

    // --- ELIMINACIÓN ---

    public function confirmDelete($id) {
        $this->alumnoIdBeingDeleted = $id;
    }

    public function delete() 
    {
        if ($this->alumnoIdBeingDeleted) {
            try {
                DB::transaction(function () {
                    $alumno = AlumnoModel::find($this->alumnoIdBeingDeleted);
                    if ($alumno) {
                        // Borramos cascada manual si no está configurada en BD
                        DB::table('pagos_matriculas')
                            ->whereIn('matricula_id', function($query) {
                                $query->select('id')->from('matriculas')->where('alumno_id', $this->alumnoIdBeingDeleted);
                            })->delete();

                        DB::table('matriculas')->where('alumno_id', $this->alumnoIdBeingDeleted)->delete();
                        
                        // Al borrar el User, si tienes onDelete('cascade'), el Alumno se borra solo.
                        User::where('id', $this->alumnoIdBeingDeleted)->delete();
                    }
                });
                session()->flash('message', 'Registro eliminado por completo.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
            }
            $this->alumnoIdBeingDeleted = null;
        }
    }

    private function resetForm() { 
        $this->reset([
            'alumno_id', 'name', 'email', 'password', 'dni', 'telefono', 'selectedAlumno', 'alumnoIdBeingDeleted'
        ]); 
        $this->resetValidation();
    }
}