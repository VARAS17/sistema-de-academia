<x-layouts::auth :title="__('Iniciar sesión')">
    <div class="fixed inset-0 flex flex-col lg:flex-row">
        
        <!-- SECCIÓN IZQUIERDA: IMAGEN -->
        <div class="relative hidden w-full lg:block lg:w-1/2 overflow-hidden">
            <img 
                src="{{ asset('pacifico.jpg') }}" 
                alt="Pacifico" 
                class="absolute inset-0 object-cover w-full h-full transition-transform duration-1000 hover:scale-110"
            >
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <!-- SECCIÓN DERECHA: FORMULARIO -->
        <div class="flex items-center justify-center w-full bg-white lg:w-1/2 dark:bg-zinc-900">
            <div class="w-full max-w-sm px-8">
                


                <div class="mb-6 text-center lg:text-left">
                    <flux:heading size="xl" level="1">{{ __('¡Bienvenido!') }}</flux:heading>
                    <flux:subheading>{{ __('Ingresa tus credenciales para acceder') }}</flux:subheading>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
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

                    <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

                    <!-- Botón VERDE Personalizado -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-3 group shadow-lg shadow-emerald-500/20">
                            <!-- Icono Puerta desde public/login/door.svg -->
                            <img 
                                src="{{ asset('sesion/door.svg') }}" 
                                class="w-6 h-6 invert brightness-0 transition-transform group-hover:translate-x-1" 
                                alt=""
                            >
                            <span class="text-base tracking-wide">{{ __('Iniciar Sesión') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts::auth>