<div class=" bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative" x-data="{ tab: @entangle('tab') }">
    
    <!-- 1. SISTEMA DE BREADCRUMBS -->
    <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            @foreach($breadcrumbs as $bc)
                <li class="inline-flex items-center">
                    @if(!$loop->first)
                        <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    @endif
                    <a href="{{ $bc['url'] ?? '#' }}" 
                       class="inline-flex items-center text-sm font-semibold transition-colors {{ $bc['url'] ? 'text-gray-500 hover:text-indigo-600' : 'text-indigo-600 cursor-default' }}">
                        @if($loop->first)
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        @endif
                        {{ $bc['name'] }}
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- MENSAJES DE ESTADO -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-white border-l-4 border-green-500 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
            <div class="flex items-center text-green-700 font-medium text-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('message') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>
    @endif

    <!-- 2. CONTENEDOR PRINCIPAL -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        
        <!-- CABECERA DE LA TARJETA (Buscador y Botón) -->
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
            @if($view === 'index')
                <!-- BUSCADOR -->
                <div class="relative group w-full sm:max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <img src="{{ asset('meta-buscar/horario.jpeg') }}" alt="Buscar" class="w-10 h-10 object-contain rounded">
                    </div>
                    <input wire:model.live.debounce.400ms="search" type="text" 
                           placeholder="Buscar por nombre, área o ciclo..." 
                           class="block w-full pl-16 pr-4 py-3 border-2 border-gray-100 rounded-2xl bg-gray-50/50 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all text-sm outline-none font-bold">
                </div>

                <!-- BOTÓN REGISTRAR -->
                @role('admin')
                <button wire:click="create"
                    class="h-14 px-6 bg-[#98FB98] text-black font-bold rounded-xl hover:bg-[#7FE67F] transition shadow-lg flex items-center justify-center active:scale-95 whitespace-nowrap">
                    <img src="{{ asset('meta-register/horario.png') }}" alt="Nuevo" class="w-10 h-10 mr-2 object-contain">
                    Nuevo Horario
                </button>
                @endrole
            @else
                <button wire:click="cancel" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition">
                    <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver al listado
                </button>
            @endif
        </div>

        <!-- CONTENIDO DINÁMICO -->
        <div class="p-6">
            @if($view === 'index')
                @role('admin')
                    <!-- TABLA ADMIN -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Nombre del Horario</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Área / Facultad</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Ciclo</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @forelse($horarios as $horario)
                                    <tr class="hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-xs">H</div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 leading-tight uppercase">{{ $horario->nombre }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">Publicado el {{ $horario->created_at->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100">
                                                {{ $horario->area->nombre }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-600">
                                            Ciclo: {{ $horario->ciclo->nombre }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center gap-1">
                                                <button wire:click="verImagen('{{ asset('storage/' . $horario->imagen) }}')" class="p-2 hover:bg-indigo-50 rounded-lg transition-colors">
                                                    <img src="{{ asset('meta-ver/horario.jpeg') }}" alt="Ver" class="w-12 h-12 object-contain">
                                                </button>
                                                <button wire:click="edit({{ $horario->id }})" class="p-2 hover:bg-amber-50 rounded-lg transition-colors">
                                                    <img src="{{ asset('meta-editar/horario.jpeg') }}" alt="Editar" class="w-12 h-12 object-contain">
                                                </button>
                                                <button wire:click="abrirConfirmacionEliminacion({{ $horario->id }})" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                    <img src="{{ asset('meta-eliminar/horario.jpeg') }}" alt="Eliminar" class="w-12 h-12 object-contain">
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">No se encontraron horarios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- VISTA CARDS PARA ALUMNOS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($horarios as $horario)
                            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 group flex flex-col">
                                <div class="relative h-64 overflow-hidden cursor-pointer" wire:click="verImagen('{{ asset('storage/' . $horario->imagen) }}')">
                                    <img src="{{ asset('storage/' . $horario->imagen) }}" class="w-full h-full object-cover object-top group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute top-4 left-4">
                                        <span class="bg-indigo-600 text-white text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest">{{ $horario->area->nombre }}</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h2 class="text-xl font-black text-gray-800 mb-6 truncate uppercase">{{ $horario->nombre }}</h2>
                                    <button wire:click="verImagen('{{ asset('storage/' . $horario->imagen) }}')" class="w-full py-3 bg-gray-50 hover:bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase transition-all border border-transparent hover:border-indigo-100">
                                        Ver Horario Completo
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center text-gray-400">No hay horarios registrados para tu ciclo académico.</div>
                        @endforelse
                    </div>
                @endrole

            @else
                <!-- FORMULARIO CREATE / EDIT -->
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter mb-10 border-b border-gray-50 pb-6">
                        {{ $horario_id ? 'Actualizar Horario' : 'Registrar Nuevo Horario' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Información General
                            </h3>
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Nombre del Horario</label>
                                <input wire:model="nombre" type="text" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 transition-all outline-none" placeholder="Ej: Horario 2024-II">
                                @error('nombre') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Área Académica</label>
                                <select wire:model.live="area_id" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 outline-none">
                                    <option value="">Seleccione el Área</option>
                                    @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                                </select>
                                @error('area_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Ciclo Académico</label>
                                <select wire:model="ciclo_id" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 outline-none">
                                    <option value="">Seleccione el Ciclo</option>
                                    @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                                </select>
                                @error('ciclo_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Imagen del Horario
                            </h3>
                            <div class="border-2 border-gray-100 border-dashed rounded-[2rem] bg-gray-50 p-6 text-center relative min-h-[300px] flex flex-col justify-center items-center">
                                @if (!$imagen && $horario_id)
                                    @php $horarioActual = \App\Models\Horario::find($horario_id); @endphp
                                    @if($horarioActual && $horarioActual->imagen)
                                        <img src="{{ asset('storage/' . $horarioActual->imagen) }}" class="mx-auto h-40 w-auto rounded-xl shadow-lg border-4 border-white mb-4">
                                    @endif
                                @endif
                                @if ($imagen)
                                    <div class="mb-4 relative">
                                        <img src="{{ $imagen->temporaryUrl() }}" class="mx-auto h-40 w-auto rounded-xl shadow-lg border-4 border-emerald-100">
                                        <button wire:click="$set('imagen', null)" class="absolute -top-2 -right-2 bg-rose-500 text-white p-1 rounded-full shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endif
                                <input type="file" wire:model="imagen" id="upload_img" accept="image/*" class="hidden">
                                <label for="upload_img" class="cursor-pointer px-6 py-3 bg-white border-2 border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:border-indigo-500 hover:text-indigo-500 transition-all">
                                    {{ ($imagen || $horario_id) ? 'Reemplazar Imagen' : 'Subir Archivo' }}
                                </label>
                                @error('imagen') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex flex-col sm:flex-row justify-end gap-4 border-t pt-8 border-gray-100">
                        <button type="button" wire:click="cancel" class="px-8 py-3 text-sm font-bold text-red-600 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 transition-all uppercase tracking-widest">
                            Cancelar
                        </button>                    
                        <button wire:click="save" wire:loading.attr="disabled" class="h-14 px-10 flex items-center justify-center bg-[#98FB98] text-black font-bold rounded-2xl hover:bg-[#7FE67F] transition shadow-xl active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove>{{ $horario_id ? 'Actualizar Horario' : 'Guardar Horario' }}</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin h-5 w-5 mr-3 text-black" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODALES EXTRAPERSONALIZADOS -->
    @if($mostrarImagenModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-md" wire:click.self="cerrarImagen">
            <button wire:click="cerrarImagen" class="absolute top-6 right-6 text-white/50 hover:text-white"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            <img src="{{ $imagenUrlActual }}" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg animate-scale-up">
        </div>
    @endif

    @if($confirmandoEliminacion)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] max-w-md w-full shadow-2xl overflow-hidden border border-gray-100 p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-rose-100 p-3 rounded-2xl text-rose-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Eliminar?</h3>
                        <p class="mt-2 text-sm text-gray-500">Esta acción es irreversible y el horario dejará de estar disponible.</p>
                    </div>
                </div>
                <div class="mt-10 flex gap-3">
                    <button wire:click="cerrarConfirmacionEliminacion" class="flex-1 px-6 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase text-[10px]">Cancelar</button>
                    <button wire:click="delete" class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl hover:bg-rose-700">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>