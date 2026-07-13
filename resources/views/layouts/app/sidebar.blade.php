<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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

            /* Item ACTIVO (Como en tu imagen, pero con texto azul para legibilidad) */
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
        </style>
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        
        <!-- Sidebar -->
        <flux:sidebar sticky collapsible="mobile" class="border-e border-blue-900">
            <flux:sidebar.header>
                <div class="flex items-center gap-2 p-1">
                    <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                </div>
                <flux:sidebar.collapse class="lg:hidden text-white" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                @role('admin')        
                    <flux:sidebar.group :heading="__('Gestión')">     
                        <flux:sidebar.item icon="folder" :href="route('CRUD.cursos')" :current="request()->routeIs('CRUD.cursos')" wire:navigate>
                            {{ __('Cursos') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="folder" :href="route('CRUD.docentes')" :current="request()->routeIs('CRUD.docentes')" wire:navigate>
                            {{ __('Docentes') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="folder" :href="route('CRUD.alumnos')" :current="request()->routeIs('CRUD.alumnos')" wire:navigate>
                            {{ __('Alumnos') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="folder" :href="route('CRUD.matriculas')" :current="request()->routeIs('CRUD.matriculas')" wire:navigate>
                            {{ __('Matrículas') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endrole    

                <flux:sidebar.group :heading="__('Registro')">
                    <flux:sidebar.item icon="folder" :href="route('CRUD.gestion-pagos')" :current="request()->routeIs('CRUD.gestion-pagos')" wire:navigate>
                        {{ __('Registro Pagos') }}
                    </flux:sidebar.item>    
                    <flux:sidebar.item icon="folder" :href="route('CRUD.asistencias')" :current="request()->routeIs('CRUD.asistencias')" wire:navigate>
                        {{ __('Asistencias') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.horarios')" :current="request()->routeIs('CRUD.horarios')" wire:navigate>
                        {{ __('Horarios') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Evaluación')">
                    @role('admin')
                        <flux:sidebar.item icon="folder" :href="route('CRUD.simulacros')" :current="request()->routeIs('CRUD.simulacros')" wire:navigate>
                            {{ __('Simulacros') }}  
                        </flux:sidebar.item>
                    @endrole
                    <flux:sidebar.item icon="folder" :href="route('CRUD.puntajes-simulacro')" :current="request()->routeIs('CRUD.puntajes-simulacro')" wire:navigate>
                        {{ __('Puntajes Simulacro') }}
                    </flux:sidebar.item>  
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="border-t border-white/10 pt-4">
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <!-- Header para Mobile -->
        <flux:header class="lg:hidden bg-[#0b2e51] border-b border-blue-900">
            <flux:sidebar.toggle class="lg:hidden text-white" icon="bars-2" inset="left" />
            <flux:spacer />
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

        <!-- LA CLAVE ESTÁ AQUÍ: Usar flux:main para envolver el slot -->
        <flux:main>
            <div class="py-6 px-4">
                {{ $slot }}
            </div>
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>