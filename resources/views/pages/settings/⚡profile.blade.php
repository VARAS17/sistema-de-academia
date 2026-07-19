<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    
    public string $telefono = '';
    public string $dni = ''; 

    public string $area_nombre = '';
    public string $ciclo_nombre = '';
    public string $carrera_nombre = '';

    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        if ($user->hasRole('alumno') && $user->alumno) {
            $alumno = $user->alumno;
            $this->telefono = $alumno->telefono ?? '';
            $this->dni = $alumno->dni ?? '';

            $matricula = $alumno->matriculas()
                ->with(['ciclo.area', 'carrera'])
                ->where('estado', 'Activa')
                ->latest()
                ->first();

            if ($matricula) {
                $this->area_nombre = $matricula->ciclo->area->nombre ?? 'N/A';
                $this->ciclo_nombre = $matricula->ciclo->nombre ?? 'N/A';
                $this->carrera_nombre = $matricula->carrera->nombre ?? 'N/A';
            }
        }
    }

    /**
     * Validación en tiempo real para el teléfono
     */
    public function updatedTelefono()
    {
        $this->validateOnly('telefono', [
            'telefono' => 'nullable|regex:/^9[0-9]{8}$/',
        ], [
            'telefono.regex' => 'El número debe empezar con 9 y tener 9 dígitos.',
        ]);
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $isAlumno = $user->hasRole('alumno');

        // Reglas de validación personalizadas
        $rules = [
            'password' => 'nullable|min:8|confirmed',
        ];

        if ($isAlumno) {
            $rules['telefono'] = 'nullable|regex:/^9[0-9]{8}$/';
        } else {
            $rules = array_merge($rules, $this->profileRules($user->id));
        }

        // Mensajes en español
        $messages = [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'telefono.regex' => 'El número de teléfono debe empezar con 9 y tener exactamente 9 dígitos.',
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El formato del correo no es válido.',
            'email.unique' => 'Este correo ya está en uso.',
        ];

        $this->validate($rules, $messages);

        // Actualizar User
        if (! $isAlumno) {
            $user->name = $this->name;
            $user->email = $this->email;
            if ($user->isDirty('email')) $user->email_verified_at = null;
        }

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        // Actualizar Alumno (Teléfono)
        if ($isAlumno && $user->alumno) {
            $user->alumno->update(['telefono' => $this->telefono]);
        }

        $this->reset(['password', 'password_confirmation']);
        Flux::toast(variant: 'success', text: __('Perfil actualizado correctamente.'));
    }

    #[Computed]
    public function isAlumno(): bool
    {
        return Auth::user()->hasRole('alumno');
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! $this->isAlumno; 
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Ajustes de perfil') }}</flux:heading>

    <x-pages::settings.layout>
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-8">
            
            <!-- Sección: Datos Personales -->
            <div class="space-y-6">
                <flux:heading level="2" size="lg">{{ __('Información Personal') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model="name" 
                        :label="__('Nombre Completo')" 
                        :readonly="$this->isAlumno"
                        :variant="$this->isAlumno ? 'filled' : null"
                    />

                    <flux:input 
                        wire:model="email" 
                        :label="__('Correo Electrónico')" 
                        :readonly="$this->isAlumno"
                        :variant="$this->isAlumno ? 'filled' : null"
                    />

                    @if($this->isAlumno)
                        <flux:input wire:model="dni" :label="__('DNI')" readonly variant="filled" />
                        <flux:input 
                            wire:model.live="telefono" 
                            :label="__('Número de Teléfono')" 
                            placeholder="9XXXXXXXX"
                            maxlength="9" 
                        />
                    @endif
                </div>
            </div>

            <!-- Sección: Información Académica (Solo para Alumnos) -->
            @if ($this->isAlumno)
                <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <flux:heading level="2" size="lg">{{ __('Información Académica') }}</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:input wire:model="area_nombre" :label="__('Área')" readonly variant="filled" />
                        <flux:input wire:model="ciclo_nombre" :label="__('Ciclo')" readonly variant="filled" />
                        <flux:input wire:model="carrera_nombre" :label="__('Carrera')" readonly variant="filled" />
                    </div>
                </div>
            @endif

            <!-- Sección: Seguridad -->
            <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:heading level="2" size="lg">{{ __('Seguridad') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model="password" 
                        :label="__('Nueva Contraseña')" 
                        type="password" 
                        placeholder="••••••••"
                        description="Dejar en blanco si no desea cambiar"
                    />
                    <flux:input 
                        wire:model="password_confirmation" 
                        :label="__('Repita la contraseña para confirmar')" 
                        type="password" 
                        placeholder="••••••••"
                    />
                </div>
            </div>

            @if ($this->hasUnverifiedEmail && ! $this->isAlumno)
                <div class="mt-4">
                    <flux:text>
                        {{ __('Tu dirección de correo no está verificada.') }}
                        <flux:link class="cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Haz clic aquí para reenviar el enlace.') }}
                        </flux:link>
                    </flux:text>
                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 text-green-600">{{ __('Enlace de verificación enviado.') }}</flux:text>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Guardar Cambios') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>