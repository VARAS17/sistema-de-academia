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
    
    // Datos de contacto/identidad
    public string $telefono = '';
    public string $dni = ''; 

    // Datos Académicos (Solo lectura)
    public string $area_nombre = '';
    public string $ciclo_nombre = '';
    public string $carrera_nombre = '';

    // Seguridad
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        // Si es alumno, cargamos datos de su tabla 'alumno' y su 'matricula'
        if ($user->hasRole('alumno') && $user->alumno) {
            $alumno = $user->alumno;
            $this->telefono = $alumno->telefono ?? '';
            $this->dni = $alumno->dni ?? '';

            // Obtenemos la matrícula activa más reciente
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
     * Update the profile information.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $isAlumno = $user->hasRole('alumno');

        $rules = [
            'password' => 'nullable|min:8|confirmed',
        ];

        if ($isAlumno) {
            $rules['telefono'] = 'nullable|digits_between:7,15';
        } else {
            $rules = array_merge($rules, $this->profileRules($user->id));
        }

        $this->validate($rules);

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
        Flux::toast(variant: 'success', text: __('Profile updated.'));
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

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout>
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-8">
            
            <!-- Sección: Datos Personales -->
            <div class="space-y-6">
                <flux:heading level="2" size="lg">{{ __('Informacion Personal') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model="name" 
                        :label="__('Nombre Completo')" 
                        :readonly="$this->isAlumno"
                        :variant="$this->isAlumno ? 'filled' : null"
                    />

                    <flux:input 
                        wire:model="email" 
                        :label="__('Correo Electronico')" 
                        :readonly="$this->isAlumno"
                        :variant="$this->isAlumno ? 'filled' : null"
                    />

                    @if($this->isAlumno)
                        <flux:input wire:model="dni" :label="__('DNI')" readonly variant="filled" />
                        <flux:input wire:model="telefono" :label="__('Numero de Telefono')" placeholder="Ej: 987654321" />
                    @endif
                </div>
            </div>

            <!-- Sección: Información Académica (Solo para Alumnos) -->
            @if ($this->isAlumno)
                <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <flux:heading level="2" size="lg">{{ __('Informacion Academica') }}</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:input wire:model="area_nombre" :label="__('Area')" readonly variant="filled" />
                        <flux:input wire:model="ciclo_nombre" :label="__('Ciclo')" readonly variant="filled" />
                        <flux:input wire:model="carrera_nombre" :label="__('Carrera')" readonly variant="filled" />
                    </div>
                </div>
            @endif

            <!-- Sección: Seguridad -->
            <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:heading level="2" size="lg">{{ __('Seguridad') }}</flux:heading>
                <flux:subheading>{{ __('Actualiza tu contraseña para mantener tu cuenta segura.') }}</flux:subheading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="password" :label="__('Nueva Contraseña')" type="password" />
                    <flux:input wire:model="password_confirmation" :label="__('Confirmar Contraseña')" type="password" />
                </div>
            </div>

            @if ($this->hasUnverifiedEmail && ! $this->isAlumno)
                <div class="mt-4">
                    <flux:text>
                        {{ __('Your email address is unverified.') }}
                        <flux:link class="cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send.') }}
                        </flux:link>
                    </flux:text>
                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 text-green-600">{{ __('Verification link sent.') }}</flux:text>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Guardar Cambios') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>