<?php

namespace App\Livewire\CRUD;

use App\Models\User;
use App\Models\Docente as DocenteModel;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Docente extends Component
{
    use WithPagination;

    public $view = 'index';

    // Propiedades de Identidad (Separadas para evitar conflictos)
    public $docente_id; // ID de la tabla 'docentes'
    public $user_id;    // ID de la tabla 'users'
    
    // Propiedades de Formulario
    public $name, $email, $password, $dni, $telefono, $especialidad, $fecha_contratacion;
    public $selectedCursos = []; 
    public $search = '';

    // Propiedades de Visualización
    public $selectedDocente; 
    public $docenteIdBeingDeleted = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . ($this->user_id ?? 'NULL'),
            'password' => $this->view == 'create' ? 'required|min:6' : 'nullable|min:6',
            'dni' => 'required|unique:docentes,dni,' . ($this->docente_id ?? 'NULL'),
            'especialidad' => 'required',
            'fecha_contratacion' => 'required|date',
            'selectedCursos' => 'required|array|min:1',
        ];
    }

    public function render()
    {
        return view('livewire.c-r-u-d.docente', [
            'docentes' => DocenteModel::with(['user', 'cursos.area', 'cursos.ciclo'])
                ->where(function($query) {
                    $query->whereHas('user', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('dni', 'like', '%' . $this->search . '%')
                    ->orWhere('especialidad', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            
            // SOLUCIÓN AL ERROR DE AGRUPACIÓN:
            // Agrupamos primero por Ciclo y luego por Área
            'allCursosGrouped' => Curso::with(['area', 'ciclo'])
                ->get()
                ->groupBy([
                    fn($item) => $item->ciclo->nombre,
                    fn($item) => $item->area->nombre
                ])
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function edit($id)
    {
        $this->resetValidation();
        $docente = DocenteModel::with('user', 'cursos')->findOrFail($id);
        
        $this->docente_id = $id;
        $this->user_id = $docente->user_id; // Guardamos el ID del usuario real
        
        $this->name = $docente->user->name;
        $this->email = $docente->user->email;
        $this->dni = $docente->dni;
        $this->telefono = $docente->telefono;
        $this->especialidad = $docente->especialidad;
        $this->fecha_contratacion = $docente->fecha_contratacion ? $docente->fecha_contratacion->format('Y-m-d') : null;
        $this->selectedCursos = $docente->cursos->pluck('id')->toArray();

        $this->view = 'edit';
    }

    public function store()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // 1. Manejo del Usuario
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                ];

                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                } elseif (!$this->user_id) {
                    $userData['password'] = Hash::make('password123'); // Password por defecto solo en creación
                }

                $user = User::updateOrCreate(['id' => $this->user_id], $userData);
                
                if (!$user->hasRole('docente')) {
                    $user->assignRole('docente');
                }

                // 2. Manejo del Perfil Docente
                $docente = DocenteModel::updateOrCreate(
                    ['user_id' => $user->id], 
                    [
                        'dni' => $this->dni,
                        'telefono' => $this->telefono,
                        'especialidad' => $this->especialidad,
                        'fecha_contratacion' => $this->fecha_contratacion,
                    ]
                );

                // 3. Sincronización de Cursos (Asignación Académica)
                $docente->cursos()->sync($this->selectedCursos);
            });

            session()->flash('message', $this->docente_id ? 'Docente actualizado correctamente.' : 'Docente registrado con éxito.');
            $this->showIndex();

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al procesar los datos: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->selectedDocente = DocenteModel::with(['user', 'cursos.area', 'cursos.ciclo'])->findOrFail($id);
        $this->view = 'show';
    }

    // --- ELIMINACIÓN ---

    public function confirmDelete($id)
    {
        // $id es el ID del Docente
        $this->docenteIdBeingDeleted = $id;
    }

    public function delete()
    {
        if ($this->docenteIdBeingDeleted) {
            try {
                $docente = DocenteModel::findOrFail($this->docenteIdBeingDeleted);
                $user = User::findOrFail($docente->user_id);
                
                // Al eliminar el usuario, se debería eliminar el docente por cascada en BD
                // o lo hacemos manual si no hay cascada:
                $docente->cursos()->detach();
                $docente->delete();
                $user->delete();

                session()->flash('message', 'Registro eliminado correctamente.');
            } catch (\Exception $e) {
                session()->flash('error', 'No se pudo eliminar el registro.');
            }
            $this->docenteIdBeingDeleted = null;
        }
    }

    public function cancelDelete()
    {
        $this->docenteIdBeingDeleted = null;
    }

    // --- NAVEGACIÓN Y RESETS ---

    public function showIndex()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    public function cancel()
    {
        $this->showIndex();
    }

    private function resetInputFields()
    {
        $this->reset([
            'name', 'email', 'password', 'dni', 'telefono', 
            'especialidad', 'fecha_contratacion', 'selectedCursos', 
            'docente_id', 'user_id', 'selectedDocente', 'docenteIdBeingDeleted'
        ]);
        $this->resetValidation();
    }
}