<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
            @role('admin')        
                <flux:sidebar.group :heading="__('Gestion')" class="grid">     
                    <flux:sidebar.item icon="folder" :href="route('CRUD.ciclos')" :current="request()->routeIs('CRUD.ciclos')" wire:navigate>
                        {{ __('Ciclos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.cursos')" :current="request()->routeIs('CRUD.cursos')" wire:navigate>
                        {{ __('Cursos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.docentes')" :current="request()->routeIs('CRUD.docentes')" wire:navigate>
                        {{ __('Docentes') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.alumnos')" :current="request()->routeIs('CRUD.alumnos')" wire:navigate>
                        {{ __('Alumnos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.carreras')" :current="request()->routeIs('CRUD.carreras')" wire:navigate>
                        {{ __('Carreras') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder" :href="route('CRUD.matriculas')" :current="request()->routeIs('CRUD.matriculas')" wire:navigate>
                        {{ __('Matrículas') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endrole    
                <flux:sidebar.group :heading="__('Registro')" class="grid">
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
                     @role('admin')
                    <flux:sidebar.item icon="folder" :href="route('CRUD.simulacros')" :current="request()->routeIs('CRUD.simulacros')" wire:navigate>
                        {{ __('Simulacros') }}  
                    </flux:sidebar.item>
                    @endrole
                    <flux:sidebar.item icon="folder" :href="route('CRUD.puntajes-simulacro')" :current="request()->routeIs('CRUD.puntajes-simulacro')" wire:navigate>
                        {{ __('Puntajes Simulacro') }}
                    </flux:sidebar.item>  

            </flux:sidebar.nav>

            <flux:spacer />



            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
