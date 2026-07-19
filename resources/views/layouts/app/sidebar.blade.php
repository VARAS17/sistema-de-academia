<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="dark font-{{ request()->cookie('font_size', 'base') }} color-{{ request()->cookie('color_mode', 'normal') }}"
      x-data
      x-on:color-mode-changed.window="document.documentElement.className =
          document.documentElement.className.replace(/color-\S+/, '') + ' color-' + $event.detail.mode"
      x-on:font-size-changed.window="document.documentElement.className =
          document.documentElement.className.replace(/font-\S+/, '') + ' font-' + $event.detail.size">
    <head>
        @include('partials.head')

        <style>
            :root {
                --brand-blue: #0042d6;       /* Azul marino profundo */
                --brand-yellow: #facc15;     /* Amarillo intenso */
            }

            /* Fondo del Sidebar */
            [data-flux-sidebar] {
                background-color: var(--brand-blue) !important;
            }

            /* Estilo para los ítems del menú */
            [data-flux-sidebar-item] {
                color: #e2e8f0 !important; /* Texto claro */
                margin-bottom: 2px;
            }

            /* Item ACTIVO */
            [data-flux-sidebar-item][data-current] {
                background-color: var(--brand-yellow) !important;
                color: #0b2e51 !important;
                font-weight: 600 !important;
            }

            /* Color de iconos en item activo */
            [data-flux-sidebar-item][data-current] [data-flux-icon] {
                color: #0b2e51 !important;
            }

            /* Hover de los items */
            [data-flux-sidebar-item]:hover:not([data-current]) {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: var(--brand-yellow) !important;
            }

            /* Títulos de grupos (Gestión, Registro) */
            [data-flux-sidebar-group] h1, [data-flux-sidebar-group] h2 {
                color: rgba(255, 255, 255, 0.5) !important;
                font-size: 0.7rem !important;
                font-weight: 700 !important;
                text-transform: uppercase;
                padding-left: 0.75rem;
                margin-top: 1rem;
            }

            /* Tamaño táctil mínimo y contraste para botones de acción */
            [data-flux-button] {
                min-width: 44px;
                min-height: 44px;
            }
        </style>
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-900">

        <svg class="hidden" aria-hidden="true">
            <defs>
                <filter id="protanopia">
                    <feColorMatrix type="matrix" values="
                        0.567, 0.433, 0,     0, 0
                        0.558, 0.442, 0,     0, 0
                        0,     0.242, 0.758, 0, 0
                        0,     0,     0,     1, 0"/>
                </filter>
                <filter id="deuteranopia">
                    <feColorMatrix type="matrix" values="
                        0.625, 0.375, 0,   0, 0
                        0.7,   0.3,   0,   0, 0
                        0,     0.3,   0.7, 0, 0
                        0,     0,     0,   1, 0"/>
                </filter>
                <filter id="tritanopia">
                    <feColorMatrix type="matrix" values="
                        0.95, 0.05,  0,     0, 0
                        0,    0.433, 0.567, 0, 0
                        0,    0.475, 0.525, 0, 0
                        0,    0,     0,     1, 0"/>
                </filter>
                <filter id="achromatopsia">
                    <feColorMatrix type="matrix" values="
                        0.299, 0.587, 0.114, 0, 0
                        0.299, 0.587, 0.114, 0, 0
                        0.299, 0.587, 0.114, 0, 0
                        0,     0,     0,     1, 0"/>
                </filter>
            </defs>
        </svg>

        <!-- Sidebar -->
        <flux:sidebar sticky collapsible="mobile" class="py-5 border-e border-blue-900">
            <!-- HEADER DEL SIDEBAR (Delimitado con borde inferior) -->
            <flux:sidebar.header class="py-5 border-b border-white/20 pb-4 mb-2 !px-0">
                <div class="flex items-center justify-center w-full">
                    <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                </div>
                <flux:sidebar.collapse class="lg:hidden text-white" />
            </flux:sidebar.header>

            <!-- CUERPO DEL SIDEBAR (Navegación) -->
            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                @role('admin')
                    <flux:sidebar.group :heading="__('Gestión')">
                        <flux:sidebar.item :href="route('CRUD.cursos')" :current="request()->routeIs('CRUD.cursos')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/curso.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                            {{ __('Cursos') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('CRUD.docentes')" :current="request()->routeIs('CRUD.docentes')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/docente.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                            {{ __('Docentes') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('CRUD.alumnos')" :current="request()->routeIs('CRUD.alumnos')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/estudiante.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                            {{ __('Alumnos') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('CRUD.matriculas')" :current="request()->routeIs('CRUD.matriculas')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/matricula.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                            {{ __('Matrículas') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endrole

                <flux:sidebar.group :heading="__('Registro')">
                    <flux:sidebar.item :href="route('CRUD.gestion-pagos')" :current="request()->routeIs('CRUD.gestion-pagos')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/pagos.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>                        
                        {{ __('Registro Pagos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('CRUD.asistencias')" :current="request()->routeIs('CRUD.asistencias')" wire:navigate>
                        <x-slot:icon><img src="{{ asset('metaforas/asistencia.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                        {{ __('Asistencias') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('CRUD.horarios')" :current="request()->routeIs('CRUD.horarios')" wire:navigate>
                        <x-slot:icon><img src="{{ asset('metaforas/horario.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                        {{ __('Horarios') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Evaluación')">
                    @role('admin')
                        <flux:sidebar.item :href="route('CRUD.simulacros')" :current="request()->routeIs('CRUD.simulacros')" wire:navigate>
                            <x-slot:icon><img src="{{ asset('metaforas/simulacros.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>
                            {{ __('Simulacros') }}
                        </flux:sidebar.item>
                    @endrole
                    <flux:sidebar.item :href="route('CRUD.puntajes-simulacro')" :current="request()->routeIs('CRUD.puntajes-simulacro')" wire:navigate>
                        <x-slot:icon><img src="{{ asset('metaforas/nota.jpeg') }}" class="w-8 h-8 rounded object-cover"></x-slot:icon>    
                    {{ __('Puntajes Simulacro') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <!-- FOOTER DEL SIDEBAR (Delimitado con borde superior) -->
            <div class="border-t border-white/20 pt-4 pb-4 px-2 flex flex-col gap-2 bg-blue-900/20">
                <livewire:accessibility-menu />
                <x-desktop-user-menu class="hidden lg:block w-full" :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <!-- Header para Mobile -->
        <flux:header class="lg:hidden bg-[#0b2e51] border-b border-blue-900">
            <flux:sidebar.toggle class="lg:hidden text-white" icon="bars-2" inset="left" />
            <flux:spacer />
            <livewire:accessibility-menu />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog">{{ __('Settings') }}</flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">{{ __('Log out') }}</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- CONTENIDO PRINCIPAL -->
        <flux:main class="flex flex-col min-h-screen">
            <!-- CUERPO DE LA PÁGINA (Body) -->
            <div class="flex-grow py-6 px-4">
                {{ $slot }}
            </div>

            <!-- FOOTER DE LA PÁGINA (Delimitado con borde superior) -->
            <footer class="mt-auto border-t border-zinc-200 dark:border-zinc-800 py-4 px-6 text-center text-xs text-zinc-500">
                <div class="flex flex-col md:flex-row justify-between items-center gap-2">
                    <p>&copy; {{ date('Y') }} Academia Metáforas. Todos los derechos reservados.</p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-indigo-600 transition">Soporte</a>
                        <a href="#" class="hover:text-indigo-600 transition">Manual de Usuario</a>
                    </div>
                </div>
            </footer>
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>