<div class=" bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative" x-data="{ tab: @entangle('tab') }">
        <div class="w-full px-4">
        
        <!-- 1. BREADCRUMBS -->
        <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <button wire:click="showIndex" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">Simulacros</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'create' ? 'Nuevo Registro' : 'Edición' }}
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ESTADO -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex justify-between items-center transition-all animate-fade-in-down">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all">

            <!-- VISTA: LISTADO -->
            @if($view == 'index')
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <img src="{{ asset('meta-buscar/simulacro.jpeg') }}" alt="Buscar" class="w-10 h-10 object-contain rounded">
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="w-full pl-16 pr-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-sm shadow-md focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none" 
                                   placeholder="Buscar simulacro...">
                        </div>
                        
                        <button wire:click="showCreate" 
                                class="w-full md:w-auto h-14 px-6 flex items-center justify-center bg-[#98FB98] text-black font-bold rounded-xl hover:bg-[#7FE67F] transition shadow-lg active:scale-95">
                            <img src="{{ asset('meta-register/simulacro.jpeg') }}"
                                alt="Nuevo Simulacro"
                                class="w-12 h-12 mr-2 object-contain">
                            Registrar Simulacro
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Información del Simulacro</th>
                                    <th class="px-6 py-4">Área Académica</th>
                                    <th class="px-6 py-4">Ciclo</th>
                                    <th class="px-6 py-4 text-center">Fecha</th>
                                    <th class="px-6 py-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($simulacros as $sim)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $sim->nombre }}</div>
                                            <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-tight">Puntaje Máx: {{ $sim->puntaje_maximo }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-gray-200">
                                                {{ $sim->area->nombre }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-600">{{ $sim->ciclo->nombre }}</td>
                                        <td class="px-6 py-4 text-center font-mono text-xs text-gray-500">
                                            {{ $sim->fecha->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-2">

                                                <!-- Editar -->
                                                <button wire:click="showEdit({{ $sim->id }})"
                                                    class="p-2 hover:bg-indigo-100 rounded-lg transition-colors"
                                                    title="Editar simulacro">

                                                    <img src="{{ asset('meta-editar/simulacro.jpeg') }}"
                                                        alt="Editar"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                                <!-- Eliminar -->
                                                <button wire:click="abrirConfirmacionEliminacion({{ $sim->id }})"
                                                    class="p-2 hover:bg-rose-50 rounded-lg transition-colors"
                                                    title="Eliminar simulacro">

                                                    <img src="{{ asset('meta-eliminar/simulacro.jpeg') }}"
                                                        alt="Eliminar"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">No se encontraron simulacros registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $simulacros->links() }}
                    </div>
                </div>

            <!-- VISTA: FORMULARIO -->
            @else
                <div class="p-8 max-w-4xl mx-auto animate-fade-in">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                                {{ $view == 'create' ? 'Registrar Simulacro' : 'Modificar Simulacro' }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Configure los parámetros del examen. Puntaje base: <span class="font-bold text-indigo-600">400.00 pts</span>.</p>
                        </div>
                        <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                    </div>

                    <form wire:submit.prevent="store" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            
                            <!-- Nombre -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nombre del Simulacro</label>
                                <input type="text" wire:model="nombre" 
                                       class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 @error('nombre') border-rose-200 bg-rose-50 @enderror" 
                                       placeholder="Ej: Simulacro Tipo Admisión - Fase I">
                                @error('nombre') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Área -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Área Académica</label>
                                <select wire:model.live="area_id" 
                                        class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-sm cursor-pointer">
                                    <option value="">Seleccione un Área...</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Ciclo -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 {{ !$area_id ? 'text-gray-300' : '' }}">Ciclo Correspondiente</label>
                                <select wire:model="ciclo_id" 
                                        class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" 
                                        {{ !$area_id ? 'disabled' : '' }}>
                                    <option value="">Seleccione un Ciclo...</option>
                                    @foreach($ciclos as $ciclo)
                                        <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('ciclo_id') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Fecha -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Fecha Programada</label>
                                <input type="date" wire:model="fecha" 
                                       class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700">
                                @error('fecha') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-8 border-t border-gray-100">
                            <button type="button" wire:click="showIndex" 
                                    class="px-8 py-3 text-sm font-bold text-red-600 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 hover:text-red-700 active:scale-95 transition-all duration-150 uppercase tracking-widest shadow-sm">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-12 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                                <span wire:loading wire:target="store" class="mr-3 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ $view == 'create' ? 'Guardar Simulacro' : 'Actualizar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- 3. MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
        @if($confirmandoEliminacion)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-[2.5rem] max-w-md w-full shadow-2xl overflow-hidden border border-gray-100">
                    <div class="p-8">
                        <div class="flex items-start">
                            <!-- Icono de Advertencia -->
                            <div class="flex-shrink-0 bg-rose-100 p-3 rounded-2xl text-rose-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            
                            <div class="ml-6">
                                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Confirmar Eliminación?</h3>
                                <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">
                                    Esta acción eliminará el simulacro de forma permanente. Tenga en cuenta que se borrarán también todos los resultados asociados de los alumnos inscritos para este examen.
                                </p>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-10 flex gap-3">
                            <button wire:click="cerrarConfirmacionEliminacion" 
                                    class="flex-1 px-6 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase text-[10px] hover:bg-gray-100 transition-all">
                                Cancelar
                            </button>
                            <button wire:click="delete" 
                                    class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl shadow-rose-200 hover:bg-rose-700 active:scale-95 transition-all">
                                Sí, Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <style>
        .animate-fade-in-down { animation: fadeInDown 0.4s ease-out; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>