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

    // Propiedades de Control de Vista
    public $view = 'index';
    public $tab = 1;

    // Propiedades de Identidad (Base de Datos)
    public $docente_id; 
    public $user_id;    
    
    // Propiedades de Formulario
    public $name, $dni, $telefono, $especialidad, $fecha_contratacion;
    public $selectedCursos = []; 
    public $search = '';

    // Propiedades de Visualización y Borrado
    public $selectedDocente; 
    public $docenteIdBeingDeleted = null;

    /**
     * Reglas de Validación
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'dni' => 'required|digits:8|unique:docentes,dni,' . ($this->docente_id ?? 'NULL'),
            'telefono' => 'required|regex:/^9[0-9]{8}$/', // Empieza con 9 y tiene 9 dígitos
            'especialidad' => 'required|string',
            'fecha_contratacion' => 'required|date',
            'selectedCursos' => 'required|array|min:1',
        ];
    }

    /**
     * Mensajes de Error en Español
     */
    protected $messages = [
        'name.required' => 'El nombre completo es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 3 caracteres.',
        'dni.required' => 'El DNI es obligatorio.',
        'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
        'dni.unique' => 'Este número de DNI ya está registrado.',
        'telefono.required' => 'El número de teléfono es obligatorio.',
        'telefono.regex' => 'El teléfono debe tener 9 dígitos y empezar con el número 9.',
        'especialidad.required' => 'La especialidad es obligatoria.',
        'fecha_contratacion.required' => 'La fecha de contratación es obligatoria.',
        'selectedCursos.required' => 'Debe seleccionar al menos un curso.',
        'selectedCursos.min' => 'Debe asignar al menos un curso al docente.',
    ];

    /**
     * Paso 1: Valida los datos del perfil antes de permitir ver los cursos
     */
    public function goToStepTwo()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'dni' => 'required|digits:8|unique:docentes,dni,' . ($this->docente_id ?? 'NULL'),
            'telefono' => 'required|regex:/^9[0-9]{8}$/',
            'especialidad' => 'required|string',
            'fecha_contratacion' => 'required|date',
        ]);

        $this->tab = 2;
    }

    /**
     * Cambiar de pestaña manualmente (con validación si se va a la 2)
     */
    public function setTab($tabNumber = 1)
    {
        if ($tabNumber == 2) {
            $this->goToStepTwo();
        } else {
            $this->tab = $tabNumber;
        }
    }

    public function render()
    {
        return view('livewire.c-r-u-d.docente', [
            'docentes' => DocenteModel::with(['user', 'cursos.area', 'cursos.ciclo'])
                ->where(function($query) {
                    $query->whereHas('user', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('dni', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            
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

    /**
     * EDIT: Corregido con $id = null para PHP 8.4
     */
    public function edit($id = null)
    {
        if (!$id) return;

        $this->resetValidation();
        $docente = DocenteModel::with('user', 'cursos')->findOrFail($id);
        
        $this->docente_id = $id;
        $this->user_id = $docente->user_id; 
        $this->name = $docente->user->name;
        $this->dni = $docente->dni;
        $this->telefono = $docente->telefono;
        $this->especialidad = $docente->especialidad;
        $this->fecha_contratacion = $docente->fecha_contratacion ? $docente->fecha_contratacion->format('Y-m-d') : null;
        $this->selectedCursos = $docente->cursos->pluck('id')->toArray();

        $this->view = 'edit';
        $this->tab = 1;
    }

    /**
     * GUARDAR / ACTUALIZAR
     */
    public function store()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // El email se genera con el DNI
                $generatedEmail = $this->dni . '@sistema.com';

                // 1. Usuario
                $userData = [
                    'name' => $this->name,
                    'email' => $generatedEmail,
                ];

                // Contraseña inicial es el DNI
                if (!$this->user_id) {
                    $userData['password'] = Hash::make($this->dni);
                }

                $user = User::updateOrCreate(['id' => $this->user_id], $userData);
                
                if (!$user->hasRole('docente')) {
                    $user->assignRole('docente');
                }

                // 2. Perfil Docente
                $docente = DocenteModel::updateOrCreate(
                    ['user_id' => $user->id], 
                    [
                        'dni' => $this->dni,
                        'telefono' => $this->telefono,
                        'especialidad' => $this->especialidad,
                        'fecha_contratacion' => $this->fecha_contratacion,
                    ]
                );

                // 3. Cursos
                $docente->cursos()->sync($this->selectedCursos);
            });

            session()->flash('message', $this->docente_id ? 'Docente actualizado correctamente.' : 'Docente registrado con éxito.');
            $this->showIndex();

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }

    /**
     * SHOW: Corregido con $id = null
     */
    public function show($id = null)
    {
        if (!$id) return;
        $this->selectedDocente = DocenteModel::with(['user', 'cursos.area', 'cursos.ciclo'])->findOrFail($id);
        $this->view = 'show';
    }

    /**
     * CONFIRM DELETE: Corregido con $id = null
     */
    public function confirmDelete($id = null)
    {
        if (!$id) return;
        $this->docenteIdBeingDeleted = $id;
    }

    public function delete()
    {
        if ($this->docenteIdBeingDeleted) {
            try {
                $docente = DocenteModel::findOrFail($this->docenteIdBeingDeleted);
                $user = User::findOrFail($docente->user_id);
                
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
            'name', 'dni', 'telefono', 'especialidad', 'fecha_contratacion', 
            'selectedCursos', 'docente_id', 'user_id', 'selectedDocente', 
            'docenteIdBeingDeleted', 'tab'
        ]);
        $this->resetValidation();
    }
}