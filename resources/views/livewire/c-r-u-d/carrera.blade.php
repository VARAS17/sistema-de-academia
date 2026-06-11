<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased relative">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
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
                        <button wire:click="volver" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">Carreras</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            @if($view == 'create') Nueva Carrera @elseif($view == 'edit') Editando Carrera @else Detalles @endif
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ÉXITO -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 font-black text-xl transition-colors">&times;</button>
            </div>
        @endif

        <!-- 2.1 MENSAJE DE ERROR (PARA INTEGRIDAD REFERENCIAL) -->
        @if (session()->has('error'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 text-orange-800 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-orange-400 hover:text-orange-600 font-black text-xl transition-colors">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">

            <!-- VISTA: INDEX (LISTADO) -->
            @if($view == 'index')
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 focus:bg-white transition-all outline-none" 
                                   placeholder="Buscar carrera o área...">
                        </div>
                        
                        <button wire:click="create" 
                                class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center justify-center active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nueva Carrera
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Nombre de la Carrera</th>
                                    <th class="px-6 py-4 text-center">Área Académica</th>
                                    <th class="px-6 py-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($carreras as $car)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $car->nombre }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-indigo-100">
                                                {{ $car->area->nombre }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-1">
                                                <button wire:click="show({{ $car->id }})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver Detalle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <button wire:click="edit({{ $car->id }})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button wire:click="confirmDelete({{ $car->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic">No se encontraron carreras registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 px-2">{{ $carreras->links() }}</div>
                </div>

            <!-- VISTA: SHOW (DETALLES) -->
            @elseif($view == 'show')
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between border-b border-gray-100 pb-8 mb-10 gap-6">
                        <div class="flex items-center space-x-6">
                            <div class="h-16 w-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-gray-800 leading-none">{{ $selectedCarrera->nombre }}</h3>
                                <div class="flex gap-2 mt-3">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-indigo-100">
                                        ÁREA: {{ $selectedCarrera->area->nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="volver" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-sm active:scale-95">Volver al Listado</button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Info Académica -->
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 border-b pb-2">Ciclos Disponibles en el Área</h4>
                                <div class="space-y-3">
                                    @forelse($selectedCarrera->ciclos as $ciclo)
                                        <div class="flex items-center text-sm font-bold text-gray-700">
                                            <span class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></span>
                                            {{ $ciclo->nombre }}
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 italic">No hay ciclos registrados para este área.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Alumnos Postulantes -->
                        <div class="lg:col-span-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Alumnos Postulando a esta Carrera</h4>
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-black text-gray-400 uppercase">Estudiante</th>
                                            <th class="px-6 py-3 text-xs font-black text-gray-400 uppercase text-center">Ciclo Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($selectedCarrera->alumnos as $alumno)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-xs mr-3">
                                                            {{ strtoupper(substr($alumno->user->name, 0, 1)) }}
                                                        </div>
                                                        <span class="font-bold text-gray-700">{{ $alumno->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold">
                                                        {{ $alumno->ciclo->nombre ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-6 py-10 text-center text-gray-400 italic">No hay alumnos postulando actualmente.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- VISTA: FORMULARIO -->
            @else
                <div class="p-8 max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl font-black text-gray-800 tracking-tight mb-8">
                        {{ $view == 'create' ? 'Registrar Carrera' : 'Actualizar Carrera' }}
                    </h2>

                    <form wire:submit.prevent="store" class="space-y-6 text-left">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Nombre de la Carrera</label>
                            <input type="text" wire:model="nombre" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none font-semibold transition-all">
                            @error('nombre') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Área Académica</label>
                            <select wire:model="area_id" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none cursor-pointer font-semibold transition-all appearance-none">
                                <option value="">Seleccione Área...</option>
                                @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                            </select>
                            @error('area_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end gap-4 mt-10 pt-8 border-t border-gray-100">
                            <button type="button" wire:click="volver" class="px-8 py-3 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">Descartar</button>
                            <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                                <span wire:loading wire:target="store" class="mr-3 animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ $view == 'create' ? 'Crear Carrera' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL DE ELIMINACIÓN CORREGIDO -->
    @if($carreraIdBeingDeleted)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelDelete"></div>

        <!-- Caja del Modal -->
        <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl transform transition-all border border-gray-100 z-[110] overflow-hidden">
            <div class="p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-red-50 border border-red-100">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Confirmar Eliminación?</h3>
                        <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">
                            Esta acción eliminará la carrera de forma permanente. Recuerde que si existen alumnos inscritos, el sistema impedirá el borrado para proteger la integridad académica.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-4 flex flex-col sm:flex-row-reverse gap-3 mt-2">
                <button wire:click="delete" type="button" class="inline-flex justify-center rounded-xl px-8 py-3 bg-red-600 text-sm font-black text-white hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-100">
                    Confirmar Eliminación
                </button>
                <button wire:click="cancelDelete" type="button" class="inline-flex justify-center rounded-xl border border-gray-200 px-8 py-3 bg-white text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">
                    Descartar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>