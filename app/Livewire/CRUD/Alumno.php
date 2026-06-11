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
    
    // Propiedades del formulario
    public $alumno_id = null; 
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $dni = null;
    public ?string $telefono = null;
    
    public $area_id = null, $ciclo_id = null, $carrera_id = null;
    
    public string $search = '';
    public $selectedAlumno;

    // Propiedad para controlar el estado del modal de eliminación
    public $alumnoIdBeingDeleted = null;

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
                ->where(function($query) {
                    $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                          ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orderByDesc('created_at')->paginate(10),
            'areas'    => Area::orderBy('nombre')->get(),
            'ciclos'   => $this->area_id ? Ciclo::where('area_id', $this->area_id)->get() : collect(),
            'carreras' => $this->area_id ? Carrera::where('area_id', $this->area_id)->get() : collect(),
        ]);
    }

    // --- ACCIONES DE NAVEGACIÓN ---

    public function show($id) {
        $this->selectedAlumno = AlumnoModel::with(['user', 'ciclo.area', 'carrera', 'cursos'])->findOrFail($id);
        $this->view = 'show';
    }

    public function create() {
        $this->resetForm();
        $this->view = 'create';
    }

    public function edit($id) {
        $this->resetForm();
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

    public function showIndex() { 
        $this->resetForm(); 
        $this->view = 'index'; 
    }

    public function cancel() { 
        $this->showIndex(); 
    }

    // --- LÓGICA DE ELIMINACIÓN (MODAL) ---

    /**
     * Prepara el ID para ser eliminado y abre el modal
     */
    public function confirmDelete($id) {
        $this->alumnoIdBeingDeleted = $id;
    }

    /**
     * Cancela la operación y cierra el modal
     */
    public function cancelDelete() {
        $this->alumnoIdBeingDeleted = null;
    }

    /**
     * Ejecuta la eliminación definitiva (sin parámetros)
     */
    public function delete() 
    {
        if ($this->alumnoIdBeingDeleted) {
            try {
                DB::transaction(function () {
                    // 1. Buscamos al alumno (recordemos que su PK es user_id)
                    $alumno = AlumnoModel::find($this->alumnoIdBeingDeleted);

                    if ($alumno) {
                        // 2. Eliminamos manualmente las matrículas (ya que el error viene de ahí)
                        // Nota: Si usas un modelo Matricula, puedes usar $alumno->matriculas()->delete()
                        DB::table('matriculas')->where('alumno_id', $alumno->user_id)->delete();

                        // 3. Opcional: Si tienes otras tablas como 'asistencias' o 'notas' que fallen, 
                        // también deberías borrarlas aquí.

                        // 4. Finalmente borramos al Usuario. 
                        // Gracias al 'onDelete(cascade)' de tu migración Alumnos, 
                        // al borrar el User se borrará automáticamente el Alumno.
                        User::where('id', $this->alumnoIdBeingDeleted)->delete();
                    }
                });

                session()->flash('message', 'Alumno y sus registros relacionados eliminados correctamente.');
            } catch (\Exception $e) {
                session()->flash('error', 'No se pudo eliminar: ' . $e->getMessage());
            }

            $this->alumnoIdBeingDeleted = null;
        }
    }

    // --- PERSISTENCIA (STORE / UPDATE) ---

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

        session()->flash('message', 'Alumno registrado exitosamente.');
        $this->showIndex();
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

        session()->flash('message', 'Perfil de alumno actualizado.');
        $this->showIndex();
    }

    private function resetForm() { 
        $this->reset([
            'alumno_id',
            'name',
            'email',
            'password',
            'dni',
            'telefono',
            'area_id',
            'ciclo_id',
            'carrera_id',
            'selectedAlumno',
            'alumnoIdBeingDeleted'
        ]); 
        $this->resetValidation();
    }
}