<div class="py-1 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative">
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
                        <button wire:click="volver" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">Cursos</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            @if($view == 'create') Nuevo Curso @elseif($view == 'edit') Editando Curso @else Detalles @endif
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
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">

            <!-- VISTA: LISTADO (INDEX) -->
            @if($view == 'index')
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 focus:bg-white transition-all outline-none" 
                                   placeholder="Buscar curso...">
                        </div>
                        
                        <button wire:click="create" 
                                class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center justify-center active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Registrar Curso
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Nombre del Curso</th>
                                    <th class="px-6 py-4">Área Académica</th>
                                    <th class="px-6 py-4">Ciclo</th>
                                    <th class="px-6 py-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($cursos as $curso)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $curso->nombre }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[11px] font-bold border border-gray-200">
                                                {{ $curso->area->nombre ?? 'Sin Área' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-[11px] font-bold border border-blue-100">
                                                {{ $curso->ciclo->nombre ?? 'Sin Ciclo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-1">
                                                <button wire:click="show({{ $curso->id }})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver Detalles">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                <button wire:click="edit({{ $curso->id }})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <!-- ACTUALIZADO: Sin mensaje de consola -->
                                                <button wire:click="confirmDelete({{ $curso->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">No se encontraron cursos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $cursos->links() }}</div>
                </div>

            <!-- VISTA: DETALLES (SHOW) -->
            @elseif($view == 'show')
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start border-b border-gray-100 pb-8 mb-8 gap-6">
                        <div>
                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1 block">Ficha Informativa del Curso</span>
                            <h3 class="text-3xl font-black text-gray-800 mb-4">{{ $selectedCurso->nombre }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold border border-gray-200">ÁREA: {{ $selectedCurso->area->nombre }}</span>
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">CICLO: {{ $selectedCurso->ciclo->nombre }}</span>
                            </div>
                        </div>
                        <button wire:click="volver" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-sm active:scale-95">Volver al Listado</button>
                    </div>

                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 ml-1 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Profesores Asignados a este Curso
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($selectedCurso->docentes as $docente)
                                <div class="bg-gray-50/50 border border-gray-100 rounded-2xl p-5 flex items-center space-x-4 hover:shadow-md transition-shadow">
                                    <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-100">
                                        {{ substr($docente->user->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-black text-gray-800 truncate">{{ $docente->user->name }}</p>
                                        <p class="text-xs text-indigo-500 font-bold">{{ $docente->especialidad }}</p>
                                        <div class="flex items-center mt-1 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0V5"></path></svg>
                                            DNI: {{ $docente->dni }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-12 border-2 border-dashed border-gray-100 rounded-3xl text-center">
                                    <p class="text-gray-400 font-bold italic text-sm">No hay profesores asignados actualmente.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            <!-- VISTA: FORMULARIO (CREATE/EDIT) -->
            @else
                <div class="p-8 max-w-4xl mx-auto">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 tracking-tight">{{ $view == 'create' ? 'Registro de Curso' : 'Edición de Curso' }}</h2>
                            <p class="text-sm text-gray-500">Gestione la asignación académica del curso.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="store" class="space-y-6">
                        <div class="group">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo del Curso</label>
                            <input wire:model="nombre" type="text" 
                                   class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none @error('nombre') border-red-200 bg-red-50 @enderror" 
                                   placeholder="Ej: Análisis Matemático I">
                            @error('nombre') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tight">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Área Académica</label>
                                <select wire:model.live="area_id" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none cursor-pointer appearance-none">
                                    <option value="">Seleccione un área...</option>
                                    @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                                </select>
                                @error('area_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ciclo de Estudios</label>
                                <select wire:model="ciclo_id" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none disabled:opacity-50 appearance-none" {{ !$area_id ? 'disabled' : '' }}>
                                    <option value="">Seleccione un ciclo...</option>
                                    @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                                </select>
                                @error('ciclo_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-8 border-t border-gray-100">
                            <button type="button" wire:click="volver" class="px-8 py-3 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Cancelar</button>
                            <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                                <span wire:loading wire:target="store" class="mr-2 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ $view == 'create' ? 'Crear Curso' : 'Actualizar Curso' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL DE ELIMINACIÓN (CON EL MISMO ESTILO) -->
    @if($cursoIdBeingDeleted)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Fondo con desenfoque -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelDelete"></div>

        <!-- Contenedor Blanco -->
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
                            Esta acción borrará el curso permanentemente. Se perderán las relaciones con los docentes asignados actualmente.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-4 flex flex-col sm:flex-row-reverse gap-3">
                <button wire:click="delete" type="button" class="inline-flex justify-center rounded-xl px-8 py-3 bg-red-600 text-sm font-black text-white hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-100">
                    Confirmar Borrado
                </button>
                <button wire:click="cancelDelete" type="button" class="inline-flex justify-center rounded-xl border border-gray-200 px-8 py-3 bg-white text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">
                    Descartar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>