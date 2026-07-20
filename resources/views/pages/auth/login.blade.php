<x-layouts::auth :title="__('Iniciar sesión')">
    <div class="fixed inset-0 flex flex-col lg:flex-row">
        
        <!-- SECCIÓN IZQUIERDA: IMAGEN -->
        <div class="relative hidden w-full lg:block lg:w-1/2 overflow-hidden">
            <img 
                src="{{ asset('pacifico.jpg') }}" 
                alt="Pacifico" 
                class="absolute inset-0 object-cover w-full h-full transition-transform duration-1000 hover:scale-110"
            >
            <div class="absolute inset-0 bg-black/20 text-white flex items-end p-12">
                <p class="text-sm opacity-70 font-light tracking-widest uppercase">Gestión de Acceso • Operaciones</p>
            </div>
        </div>

        <!-- SECCIÓN DERECHA: FORMULARIO -->
        <div class="flex items-center justify-center w-full bg-white lg:w-1/2 dark:bg-zinc-900">
            <div class="w-full max-w-sm px-8" 
                 x-data="{ 
                    loading: false, 
                    timeout: false,
                    startLoading() {
                        this.loading = true;
                        // Si después de 6 segundos no ha redireccionado, mostrar mensaje de espera larga
                        setTimeout(() => { if(this.loading) this.timeout = true }, 6000);
                    }
                 }">
                
                <div class="mb-8 text-center lg:text-left">
                    <flux:heading size="xl" level="1" class="font-bold tracking-tight">{{ __('¡Bienvenido!') }}</flux:heading>
                    <flux:subheading>{{ __('Ingresa tus credenciales para acceder al sistema') }}</flux:subheading>
                </div>

                <form method="POST" action="{{ route('login.store') }}" @submit="startLoading()" class="flex flex-col gap-5">
                    @csrf

                    <!-- Correo electrónico -->
                    <div>
                        <flux:input
                            name="email"
                            :label="__('Correo electrónico')"
                            :value="old('email')"
                            type="email"
                            required
                            autofocus
                            placeholder="ejemplo@correo.com"
                        >
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </x-slot>
                        </flux:input>
                        @error('email') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <flux:input
                            name="password"
                            :label="__('Contraseña')"
                            type="password"
                            required
                            :placeholder="__('Tu contraseña')"
                            viewable
                        >
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </x-slot>
                        </flux:input>
                        @error('password') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />
                        {{-- Opcional: <a href="#" class="text-sm text-emerald-600 hover:underline">¿Olvidaste tu contraseña?</a> --}}
                    </div>

                    <!-- Botón Dinámico -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="relative w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-3 group shadow-lg shadow-emerald-500/30 overflow-hidden"
                        >
                            <!-- Spinner de carga -->
                            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- Icono Puerta (Solo visible si no está cargando) -->
                            <img 
                                x-show="!loading"
                                src="{{ asset('sesion/door.svg') }}" 
                                class="w-6 h-6 invert brightness-0 transition-transform group-hover:translate-x-1" 
                                alt=""
                            >

                            <!-- Textos cambiantes -->
                            <span class="text-base tracking-wide" x-text="loading ? (timeout ? '{{ __('Todavía cargando...') }}' : '{{ __('Verificando...') }}') : '{{ __('Iniciar Sesión') }}'"></span>
                        </button>

                        <!-- Mensaje de espera prolongada -->
                        <div 
                            x-show="timeout" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            class="mt-4 p-4 rounded-lg bg-amber-50 border border-amber-100 flex gap-3 items-center"
                        >
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-amber-800 leading-tight">
                                    {{ __('El servidor está tardando más de lo normal. Por favor, mantenga esta ventana abierta.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </form>

                <footer class="mt-8 text-center">
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                    </p>
                </footer>
            </div>
        </div>
    </div>
</x-layouts::auth>