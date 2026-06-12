<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    
    <!-- 1. SECCIÓN DE BREADCRUMBS -->
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

    @if($view === 'index')
        <!-- VISTA DE LISTADO -->
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">Horarios Académicos</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión y consulta de cronogramas por facultades y ciclos.</p>
            </div>

            @role('admin')
            <button wire:click="create" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-100 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Horario
            </button>
            @endrole
        </div>

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
                <select wire:model.live="filtro_ciclo" class="w-full bg-gray-50 border-2 border-gray-50 rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none p-3 cursor-pointer">
                    <option value="">Todos los Ciclos</option>
                    @foreach($ciclos_filtro as $ciclo)
                        <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end pb-1">
                <button wire:click="$set('filtro_area', null), $set('filtro_ciclo', null)" class="text-xs font-black text-rose-500 hover:text-rose-700 transition-colors uppercase tracking-widest flex items-center">
                    Limpiar Filtros
                </button>
            </div>
        </div>
        @endrole

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex items-center animate-fade-in-down">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in-down">
            @forelse($horarios as $horario)
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 group flex flex-col">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('storage/' . $horario->imagen) }}" class="w-full h-full object-cover object-top group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="bg-indigo-600 text-white text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-xl">{{ $horario->area->nombre }}</span>
                            <span class="bg-white/90 backdrop-blur-md text-gray-800 text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-lg border border-white/20">Ciclo: {{ $horario->ciclo->nombre }}</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h2 class="text-xl font-black text-gray-800 mb-6 truncate tracking-tight uppercase">{{ $horario->nombre }}</h2>
                        @role('admin')
                        <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                            <button wire:click="edit({{ $horario->id }})" class="flex items-center text-xs font-black text-indigo-500 hover:text-indigo-700 uppercase tracking-widest transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar
                            </button>
                            <!-- BOTÓN DISPARADOR DEL MODAL -->
                            <button wire:click="abrirConfirmacionEliminacion({{ $horario->id }})" class="flex items-center text-xs font-black text-rose-400 hover:text-rose-600 uppercase tracking-widest transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        </div>
                        @endrole
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 text-center">
                    <p class="text-gray-400 font-bold">No hay horarios registrados.</p>
                </div>
            @endforelse
        </div>

    @else
        <!-- VISTA DE FORMULARIO -->
        <div class="max-w-3xl mx-auto animate-fade-in-down">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 p-8 sm:p-12">
                <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">{{ $horario_id ? 'Actualizar Horario' : 'Nuevo Horario' }}</h3>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-1">SGA Registro Académico</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Nombre del Horario</label>
                        <input type="text" wire:model="nombre" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-sm focus:border-indigo-500 transition-all outline-none">
                        @error('nombre') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Área</label>
                            <select wire:model.live="area_id" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-xs uppercase focus:border-indigo-500 outline-none">
                                <option value="">Seleccione Área</option>
                                @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Ciclo</label>
                            <select wire:model="ciclo_id" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-xs uppercase focus:border-indigo-500 outline-none">
                                <option value="">Seleccione Ciclo</option>
                                @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Imagen del Horario</label>
                        <div class="border-2 border-gray-100 border-dashed rounded-[2rem] bg-gray-50 p-10 text-center">
                            @if ($imagen)
                                <img src="{{ $imagen->temporaryUrl() }}" class="mx-auto h-56 w-auto rounded-2xl shadow-xl mb-4">
                            @endif
                            <input type="file" wire:model="imagen" class="text-xs font-black uppercase text-indigo-600">
                            @error('imagen') <p class="text-rose-500 text-[10px] font-black uppercase mt-2">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-14 flex justify-end gap-6 border-t border-gray-50 pt-10">
                    <button wire:click="cancel" class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Cancelar</button>
                    <button wire:click="save" class="px-12 py-4 bg-indigo-600 text-white font-black text-xs uppercase rounded-2xl shadow-2xl active:scale-95 transition-all">Guardar Horario</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
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
                                Esta acción eliminará el horario de forma permanente del sistema. Recuerde que una vez borrado, los alumnos no podrán visualizar su cronograma de clases asignado.
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

    <style>
        .animate-fade-in-down { animation: fadeInDown 0.4s ease-out; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>