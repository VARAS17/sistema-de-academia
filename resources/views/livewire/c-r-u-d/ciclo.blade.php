<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- SECCIÓN: BREADCRUMBS -->
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
                        <button wire:click="volver" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2">Ciclos</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-bold text-gray-400 md:ml-2 uppercase tracking-wider">
                            @if($view == 'create') Nuevo Registro @elseif($view == 'edit') Edición @else Detalles @endif
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- MENSAJES FLASH -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 shadow-sm mb-6 rounded-r-lg flex justify-between items-center transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-bold">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">

            @if($view == 'index')
                <!-- VISTA DE LISTADO -->
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="relative w-full md:w-1/3">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live="search" type="text" placeholder="Buscar ciclo o área..." 
                                   class="pl-10 block w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>
                        <button wire:click="create()" 
                                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl transition duration-200 shadow-lg shadow-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Nuevo Ciclo
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nombre del Ciclo</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Área</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Aula</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Estado</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ciclos as $ciclo)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-800">{{ $ciclo->nombre }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                                            {{ $ciclo->area->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600 font-medium">
                                        <span class="flex items-center justify-center italic">
                                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $ciclo->aula }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ciclo->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $ciclo->activo ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $ciclo->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-1">
                                            <button wire:click="show({{ $ciclo->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ver Detalles">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button wire:click="edit({{ $ciclo->id }})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button wire:click="delete({{ $ciclo->id }})" onclick="confirm('¿Seguro que desea eliminar?') || event.stopImmediatePropagation()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Borrar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $ciclos->links() }}
                    </div>
                </div>

            @elseif($view == 'show')
                <!-- VISTA DE DETALLES (NUEVA) -->
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start border-b border-gray-100 pb-8 mb-8 gap-6">
                        <div>
                            <h3 class="text-3xl font-black text-gray-800 mb-2">{{ $selectedCiclo->nombre }}</h3>
                            <div class="flex flex-wrap gap-3">
                                <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-wider">
                                    {{ $selectedCiclo->area->nombre }}
                                </span>
                                <span class="px-4 py-1.5 {{ $selectedCiclo->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} rounded-xl text-xs font-black uppercase tracking-wider">
                                    {{ $selectedCiclo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                        <button wire:click="volver" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-sm">
                            Volver al Listado
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Columna de Información General -->
                        <div class="md:col-span-1 space-y-6">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Información de Ubicación</h4>
                                <div class="flex items-center text-gray-700">
                                    <div class="p-3 bg-white rounded-lg shadow-sm mr-4">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase">Aula Asignada</p>
                                        <p class="text-lg font-bold text-gray-800">{{ $selectedCiclo->aula }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de Profesores -->
                        <div class="md:col-span-2">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Profesores a Cargo de este Ciclo</h4>
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Docente</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Especialidad</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Cursos</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($selectedCiclo->cursos->flatMap->docentes->unique('user_id') as $docente)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-xs mr-3">
                                                            {{ substr($docente->user->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-sm font-bold text-gray-700">{{ $docente->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $docente->especialidad ?? 'No especificada' }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($docente->cursos->where('ciclo_id', $selectedCiclo->id) as $curso)
                                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold border border-gray-200">
                                                                {{ $curso->nombre }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                                    No hay docentes asignados a cursos en este ciclo.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- VISTA DE FORMULARIO (Create/Edit) -->
                <div class="p-8 max-w-2xl mx-auto">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-black text-gray-800">
                            {{ $view == 'create' ? 'Nuevo Ciclo' : 'Editar Ciclo' }}
                        </h3>
                        <p class="text-gray-500 text-sm">Complete la información requerida para el ciclo académico.</p>
                    </div>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre del Ciclo</label>
                            <input type="text" wire:model="nombre" class="w-full p-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none @error('nombre') border-red-400 @enderror" placeholder="Ej: 2024-II Verano">
                            @error('nombre') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Área Académica</label>
                            <select wire:model="area_id" class="w-full p-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                                <option value="">Seleccione el Área...</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                            @error('area_id') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Aula / Ubicación</label>
                            <input type="text" wire:model="aula" class="w-full p-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none" placeholder="Ej: Pabellón A - 102">
                            @error('aula') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 flex items-center justify-between">
                            <label for="activo" class="text-sm font-bold text-indigo-900 cursor-pointer">¿El ciclo se encuentra activo actualmente?</label>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" wire:model="activo" id="activo" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer checked:right-0 checked:border-indigo-600 transition-all"/>
                                <label for="activo" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-all"></label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4 border-t border-gray-100 pt-6">
                        <button wire:click="volver" type="button" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                            Descartar
                        </button>
                        <button wire:click.prevent="store()" type="button" 
                                class="px-10 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                            <span wire:loading wire:target="store" class="mr-2 italic">Procesando...</span>
                            {{ $view == 'create' ? 'Guardar Ciclo' : 'Actualizar Cambios' }}
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Estilos adicionales para el Toggle -->
    <style>
        .toggle-checkbox:checked { right: 0; border-color: #4f46e5; }
        .toggle-checkbox:checked + .toggle-label { background-color: #4f46e5; }
        .toggle-checkbox { right: 50%; transition: all 0.3s; }
    </style>
</div>