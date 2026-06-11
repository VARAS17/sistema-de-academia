<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    
    <!-- 1. SECCIÓN DE BREADCRUMBS -->
    <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            @foreach($breadcrumbs as $bc)
                <li class="inline-flex items-center">
                    @if(!$loop->first)
                        <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    @endif
                    <a @if(isset($bc['action'])) wire:click="{{ $bc['action'] }}" @endif 
                       href="{{ $bc['url'] ?? '#' }}" 
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

    @if($view === 'index')
        <!-- VISTA DE LISTADO -->
        
        <!-- 2. ENCABEZADO Y ACCIONES -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tighter">Horarios Académicos</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión y consulta de cronogramas por facultades y ciclos.</p>
            </div>

            @role('admin')
            <button wire:click="create" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-100 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Horario
            </button>
            @endrole
        </div>

        <!-- 3. BARRA DE FILTROS -->
        @role('admin')
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm animate-fade-in-down">
            <div class="space-y-1">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Filtrar por Área</label>
                <select wire:model.live="filtro_area" class="w-full bg-gray-50 border-2 border-gray-50 rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none p-3 cursor-pointer">
                    <option value="">Todas las Áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Filtrar por Ciclo</label>
                <select wire:model.live="filtro_cycle" class="w-full bg-gray-50 border-2 border-gray-50 rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none p-3 cursor-pointer {{ empty($ciclos_filtro) ? 'opacity-50' : '' }}" {{ empty($ciclos_filtro) ? 'disabled' : '' }}>
                    <option value="">{{ empty($ciclos_filtro) ? 'Seleccione área primero' : 'Todos los Ciclos' }}</option>
                    @foreach($ciclos_filtro as $ciclo)
                        <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end pb-1">
                <button wire:click="$set('filtro_area', null), $set('filtro_ciclo', null)" class="text-xs font-black text-rose-500 hover:text-rose-700 transition-colors uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Limpiar Filtros
                </button>
            </div>
        </div>
        @endrole

        <!-- 4. MENSAJES DE ESTADO -->
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

        <!-- 5. GRID DE HORARIOS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in-down">
            @forelse($horarios as $horario)
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 group flex flex-col">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('storage/' . $horario->imagen) }}" alt="{{ $horario->nombre }}" class="w-full h-full object-cover object-top group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="bg-indigo-600 text-white text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-xl">
                                {{ $horario->area->nombre }}
                            </span>
                            <span class="bg-white/90 backdrop-blur-md text-gray-800 text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-lg border border-white/20">
                                Ciclo: {{ $horario->ciclo->nombre }}
                            </span>
                        </div>
                        <div class="absolute inset-0 bg-indigo-900/40 opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center">
                            <a href="{{ asset('storage/' . $horario->imagen) }}" target="_blank" class="bg-white text-indigo-600 p-4 rounded-2xl shadow-2xl transform scale-50 group-hover:scale-100 transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            </a>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h2 class="text-xl font-black text-gray-800 mb-6 truncate tracking-tight group-hover:text-indigo-600 transition-colors">{{ $horario->nombre }}</h2>
                        @role('admin')
                        <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                            <button wire:click="edit({{ $horario->id }})" class="flex items-center text-xs font-black text-indigo-500 hover:text-indigo-700 uppercase tracking-widest transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar
                            </button>
                            <button onclick="confirm('¿Estás seguro de eliminar este horario?') || event.stopImmediatePropagation()" 
                                    wire:click="delete({{ $horario->id }})" 
                                    class="flex items-center text-xs font-black text-rose-400 hover:text-rose-600 uppercase tracking-widest transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        </div>
                        @endrole
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold text-lg">No hay horarios disponibles.</p>
                </div>
            @endforelse
        </div>

    @else
        <!-- VISTA DE FORMULARIO (ESTILO MODAL AHORA EN VISTA) -->
        <div class="max-w-3xl mx-auto animate-fade-in-down">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
                <div class="p-8 sm:p-12">
                    <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-800 tracking-tighter">
                                {{ $horario_id ? 'Actualizar Horario' : 'Nuevo Horario' }}
                            </h3>
                            <p class="text-xs text-gray-400 uppercase font-black tracking-widest mt-1">Formulario de registro académico</p>
                        </div>
                        <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Nombre -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nombre Descriptivo</label>
                            <input type="text" wire:model="nombre" placeholder="Ej: Horario Ciclo 2024-II" 
                                class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none">
                            @error('nombre') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Área -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Área Académica</label>
                                <select wire:model.live="area_id" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-xs uppercase focus:bg-white focus:border-indigo-500 transition-all outline-none cursor-pointer">
                                    <option value="">Seleccione Área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Ciclo -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ciclo</label>
                                <select wire:model="ciclo_id" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-xs uppercase focus:bg-white focus:border-indigo-500 transition-all outline-none cursor-pointer {{ empty($ciclos) ? 'opacity-50' : '' }}" {{ empty($ciclos) ? 'disabled' : '' }}>
                                    <option value="">Seleccione Ciclo</option>
                                    @foreach($ciclos as $ciclo)
                                        <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('ciclo_id') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cargar Imagen de Horario</label>
                            <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-100 border-dashed rounded-[2rem] bg-gray-50 hover:bg-indigo-50/50 hover:border-indigo-300 transition-all group relative">
                                <div class="space-y-2 text-center">
                                    @if ($imagen)
                                        <img src="{{ $imagen->temporaryUrl() }}" class="mx-auto h-56 w-auto rounded-2xl shadow-2xl mb-4">
                                    @elseif($horario_id)
                                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 italic">Imagen actual cargada. Seleccione otra para cambiarla.</p>
                                    @else
                                        <div class="mx-auto h-20 w-20 bg-white rounded-3xl shadow-sm flex items-center justify-center text-gray-300 group-hover:text-indigo-500 transition-colors">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="flex text-xs text-gray-600 justify-center pt-2">
                                        <label class="relative cursor-pointer font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">
                                            <span>Seleccionar archivo</span>
                                            <input type="file" wire:model="imagen" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter italic">PNG, JPG, JPEG hasta 2MB</p>
                                </div>
                            </div>
                            <div wire:loading wire:target="imagen" class="text-[10px] font-black text-indigo-500 mt-2 animate-pulse uppercase tracking-widest italic text-center">Subiendo archivo al servidor...</div>
                            @error('imagen') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1 tracking-tight">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-14 flex flex-col sm:flex-row justify-end gap-6 border-t border-gray-50 pt-10">
                        <button wire:click="cancel" class="px-8 py-4 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                            Cancelar y volver
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled" class="px-12 py-4 bg-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-indigo-700 shadow-2xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                            <span wire:loading.remove wire:target="save">Guardar Cambios</span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 7. FOOTER -->
    <div class="mt-12 text-center">
        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">SGA • Control de Horarios Académicos v2.5</p>
    </div>

    <style>
        .animate-fade-in-down { animation: fadeInDown 0.4s ease-out; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>