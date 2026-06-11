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

    // Propiedades Identidad y Perfil
    public $name, $email, $password, $docente_id;
    public $dni, $telefono, $especialidad, $fecha_contratacion;
    public $selectedDocente; 
    public $selectedCursos = []; 
    public $search = '';

    // NUEVO: Propiedad para el modal de eliminación
    public $docenteIdBeingDeleted = null;

    public function mount($id = null)
    {
        if ($id) { $this->edit($id); }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . ($this->docente_id ?? 'NULL'),
            'password' => $this->view == 'create' ? 'required|min:6' : 'nullable|min:6',
            'dni' => 'required|unique:docentes,dni,' . ($this->docente_id ?? 'NULL') . ',user_id',
            'especialidad' => 'required',
            'fecha_contratacion' => 'required|date',
            'selectedCursos' => 'required|array|min:1',
        ];
    }

    public function render()
    {
        return view('livewire.c-r-u-d.docente', [
            'docentes' => DocenteModel::with(['user', 'cursos.area'])
                ->where(function($query) {
                    $query->whereHas('user', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('dni', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            
            'allCursosGrouped' => Curso::with(['area', 'ciclo'])->get()->groupBy(function($item) {
                return $item->area->nombre;
            })
        ]);
    }

    public function show($id)
    {
        $this->selectedDocente = DocenteModel::with(['user', 'cursos.area', 'cursos.ciclo'])->findOrFail($id);
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
        $docente = DocenteModel::with('user', 'cursos')->findOrFail($id);
        
        $this->docente_id = $id;
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

        DB::transaction(function () {
            $userData = ['name' => $this->name, 'email' => $this->email];
            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            } elseif (!$this->docente_id) {
                $userData['password'] = Hash::make('password123');
            }

            // Aquí se usa docente_id que en tu lógica parece representar el ID del User
            $user = User::updateOrCreate(['id' => $this->docente_id], $userData);
            if (!$user->hasRole('docente')) { $user->assignRole('docente'); }

            $docente = DocenteModel::updateOrCreate(
                ['user_id' => $user->id], 
                [
                    'dni' => $this->dni,
                    'telefono' => $this->telefono,
                    'especialidad' => $this->especialidad,
                    'fecha_contratacion' => $this->fecha_contratacion,
                ]
            );
            $docente->cursos()->sync($this->selectedCursos);
        });

        session()->flash('message', $this->docente_id ? 'Docente actualizado.' : 'Docente registrado.');
        $this->showIndex();
    }

    // --- NUEVOS MÉTODOS PARA EL MODAL DE ELIMINACIÓN ---

    public function confirmDelete($id)
    {
        // En tu lógica, el ID que recibes para eliminar es el del User
        $this->docenteIdBeingDeleted = $id;
    }

    public function cancelDelete()
    {
        $this->docenteIdBeingDeleted = null;
    }

    public function delete()
    {
        if ($this->docenteIdBeingDeleted) {
            $user = User::findOrFail($this->docenteIdBeingDeleted);
            $user->delete();
            session()->flash('message', 'Docente eliminado.');
            $this->docenteIdBeingDeleted = null;
        }
    }

    // --- MÉTODOS DE NAVEGACIÓN ---

    public function showIndex()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    public function cancel()
    {
        $this->showIndex();
    }

    public function volver()
    {
        $this->showIndex();
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'email', 'password', 'dni', 'telefono', 'especialidad', 'fecha_contratacion', 'selectedCursos', 'docente_id', 'selectedDocente', 'docenteIdBeingDeleted']);
        $this->resetValidation();
    }
}