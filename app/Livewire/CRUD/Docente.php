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

    // Control de vista: 'index', 'create', 'edit'
    public $view = 'index';

    // Propiedades Identidad
    public $name, $email, $password, $docente_id;
    
    // Propiedades Perfil
    public $dni, $telefono, $especialidad, $fecha_contratacion;
    
    // Carga Académica
    public $selectedCursos = []; 

    public $search = '';

    // El error "Unable to resolve dependency" se soluciona haciendo el ID opcional en el mount
    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        }
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

    public function create()
    {
        $this->resetInputFields();
        $this->view = 'create';
    }

    public function edit($id)
    {
        $this->resetValidation();
        // Buscamos por user_id ya que es la PK en el modelo Docente
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
            // 1. Preparar datos de Usuario
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            // Solo actualizar password si se proporcionó una nueva
            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            } elseif (!$this->docente_id) {
                $userData['password'] = Hash::make('password123');
            }

            // 2. Identidad (User)
            $user = User::updateOrCreate(
                ['id' => $this->docente_id], // docente_id es el user_id
                $userData
            );

            // 3. Rol Spatie
            if (!$user->hasRole('docente')) {
                $user->assignRole('docente');
            }

            // 4. Perfil (Docente)
            $docente = DocenteModel::updateOrCreate(
                ['user_id' => $user->id], 
                [
                    'dni' => $this->dni,
                    'telefono' => $this->telefono,
                    'especialidad' => $this->especialidad,
                    'fecha_contratacion' => $this->fecha_contratacion,
                ]
            );

            // 5. Pivot Cursos
            $docente->cursos()->sync($this->selectedCursos);
        });

        session()->flash('message', $this->docente_id ? 'Docente actualizado exitosamente.' : 'Docente registrado correctamente.');
        $this->showIndex();
    }

    public function delete($id)
    {
        // Al borrar el User, por la migración onDelete('cascade'), se borra el Docente
        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('message', 'Docente eliminado correctamente.');
    }

    public function cancel()
    {
        $this->showIndex();
    }

    public function showIndex()
    {
        $this->resetInputFields();
        $this->view = 'index';
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'email', 'password', 'dni', 'telefono', 'especialidad', 'fecha_contratacion', 'selectedCursos', 'docente_id']);
        $this->resetValidation();
    }
}