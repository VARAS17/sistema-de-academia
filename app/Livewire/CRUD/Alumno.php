<?php

namespace App\Livewire\CRUD;

use App\Models\User;
use App\Models\Alumno as AlumnoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\ResultadoSimulacro;
use App\Models\Asistencia;
use App\Models\Matricula;
use App\Models\PagoMatricula;

class Alumno extends Component
{
    use WithPagination;

    public string $view = 'index';
    
    public $alumno_id = null; 
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $dni = null;
    public ?string $telefono = null;
    
    public string $search = '';
    public $selectedAlumno;
    public $alumnoIdBeingDeleted = null;

    // 1. REGLAS DE VALIDACIÓN ACTUALIZADAS
    protected function rules(): array
    {
        return [
            'name'      => 'required|string|min:3|max:255',
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($this->alumno_id)],
            'password'  => $this->view === 'create' ? 'required|min:6' : 'nullable|min:6',
            'dni'       => [
                'required', 
                'digits:8', // Exactamente 8 dígitos
                Rule::unique('alumnos', 'dni')->ignore($this->alumno_id, 'user_id')
            ],
            'telefono'  => [
                'nullable', 
                'digits:9',    // Debe tener 9 dígitos
                'regex:/^9/'   // Debe empezar por 9
            ],
        ];
    }

    // 2. MENSAJES PERSONALIZADOS EN ESPAÑOL
    protected $messages = [
        'name.required'     => ' El nombre es obligatorio.',
        'name.min'          => ' El nombre debe tener al menos 3 caracteres.',
        'email.required'    => ' El correo electrónico es obligatorio.',
        'email.email'       => ' El formato del correo no es válido.',
        'email.unique'      => ' Este correo ya está registrado.',
        'password.required' => ' La contraseña es obligatoria.',
        'password.min'      => ' La contraseña debe tener al menos 6 caracteres.',
        'dni.required'      => ' El DNI es obligatorio.',
        'dni.digits'        => ' El DNI debe tener exactamente 8 dígitos.',
        'dni.unique'        => ' Este DNI ya está registrado.',
        'telefono.digits'   => ' El teléfono debe tener 9 dígitos.',
        'telefono.regex'    => ' El teléfono debe empezar con el número 9.',
    ];

    // El resto de tus métodos (render, show, create, store, etc.) se mantienen igual...
    
    public function render()
    {
        return view('livewire.c-r-u-d.alumno', [
            'alumnos'  => AlumnoModel::with(['user', 'matriculas.ciclo'])
                ->where(function($query) {
                    $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                          ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orderByDesc('created_at')
                ->paginate(10),
        ]);
    }

    public function show($id) {
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
            ]);
        });

        session()->flash('message', 'Alumno creado correctamente.');
        $this->cancel();
    }

    public function update() {
        $this->validate();
        $alumno = AlumnoModel::where('user_id', $this->alumno_id)->firstOrFail();
        
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

    public function confirmDelete($id) {
        $this->alumnoIdBeingDeleted = $id;
    }

public function delete() 
{
    if (!$this->alumnoIdBeingDeleted) return;

    try {
        $id = $this->alumnoIdBeingDeleted;

        DB::transaction(function () use ($id) {
            // Detectamos nombres de tablas dinámicamente desde los modelos
            $tablaResultados = (new ResultadoSimulacro())->getTable();
            $tablaAsistencias = (new Asistencia())->getTable();
            $tablaMatriculas = (new Matricula())->getTable();
            $tablaPagos = (new PagoMatricula())->getTable();

            // 1. Borrar resultados de simulacros
            DB::table($tablaResultados)->where('alumno_id', $id)->delete();

            // 2. Borrar asistencias
            DB::table($tablaAsistencias)->where('alumno_id', $id)->delete();

            // 3. Borrar pagos (vía matrículas)
            $matriculaIds = DB::table($tablaMatriculas)->where('alumno_id', $id)->pluck('id');
            DB::table($tablaPagos)->whereIn('matricula_id', $matriculaIds)->delete();

            // 4. Borrar matrículas
            DB::table($tablaMatriculas)->where('alumno_id', $id)->delete();

            // 5. Borrar el registro del alumno (en la tabla 'alumnos')
            DB::table('alumnos')->where('user_id', $id)->delete();

            // 6. Finalmente borrar el usuario base
            DB::table('users')->where('id', $id)->delete();
        });

        session()->flash('message', 'Estudiante y todo su historial eliminados con éxito.');
        $this->view = 'index';
        
    } catch (\Exception $e) {
        // Si una tabla no existe, la saltamos o mostramos el error específico
        session()->flash('error', 'Error al borrar: ' . $e->getMessage());
    }

    $this->alumnoIdBeingDeleted = null;
}

    private function resetForm() { 
        $this->reset([
            'alumno_id', 'name', 'email', 'password', 'dni', 'telefono', 'selectedAlumno', 'alumnoIdBeingDeleted'
        ]); 
        $this->resetValidation();
    }
}