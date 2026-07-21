<div class=" bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative" x-data="{ tab: @entangle('tab') }">
    <div class="w-full  px-4">
            <!-- 1. SISTEMA DE BREADCRUMBS -->
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
                    <button wire:click="setView('list')" class="ml-1 text-sm font-semibold {{ $view == 'list' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">
                        Gestión de Puntajes
                    </button>
                </div>
            </li>
            @if($view !== 'list')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'edit' ? 'Registrar Nota' : 'Detalle de Resultado' }}
                        </span>
                    </div>
                </li>
            @endif
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
        
        <!-- CABECERA DE LA TARJETA (Filtros o Botón Volver) -->
        <div class="p-6 border-b border-gray-50 bg-white">
            @if($view === 'list')
                @hasanyrole('admin|docente')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Área Académica</label>
                            <select wire:model.live="area_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl font-bold text-sm p-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                                <option value="">Seleccione Área...</option>
                                @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ciclo</label>
                            <select wire:model.live="ciclo_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl font-bold text-sm p-3 disabled:opacity-40 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" {{ !$area_id ? 'disabled' : '' }}>
                                <option value="">Seleccione Ciclo...</option>
                                @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Simulacro</label>
                            <select wire:model.live="simulacro_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl font-bold text-sm p-3 disabled:opacity-40 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" {{ !$ciclo_id ? 'disabled' : '' }}>
                                <option value="">Seleccione Simulacro...</option>
                                @foreach($simulacros as $sim) <option value="{{ $sim->id }}">{{ $sim->nombre }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                @endhasanyrole
                @hasrole('alumno')
                    <h3 class="text-xl font-black text-gray-800 uppercase italic tracking-tighter">Mi Historial de Simulacros</h3>
                @endhasrole
            @else
                <button wire:click="setView('list')" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition">
                    <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver al listado
                </button>
            @endif
        </div>

        <!-- CONTENIDO DINÁMICO -->
        <div class="p-6">
            @hasanyrole('admin|docente')
                @if($view === 'list')
                    @if($simulacro_id)
                        <!-- BUSCADOR Y ORDENAMIENTO DENTRO DEL LISTADO -->
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                            <div class="relative w-full md:w-1/2 group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por Nombre o DNI..." 
                                       class="w-full pl-12 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-sm focus:border-indigo-500 outline-none transition-all font-bold">
                            </div>
                            <button wire:click="toggleSort" class="flex items-center gap-2 px-6 py-2.5 bg-white text-gray-600 border-2 border-gray-200 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                                <span>Puntaje: {{ $sortScore === 'desc' ? 'DESC' : 'ASC' }}</span>
                                <svg class="w-4 h-4 {{ $sortScore === 'asc' ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <!-- TABLA DE RESULTADOS -->
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                    <tr>
                                        <th class="px-6 py-4">Estudiante</th>
                                        <th class="px-6 py-4">DNI</th>
                                        <th class="px-6 py-4 text-center">Puntaje</th>
                                        <th class="px-6 py-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($alumnos as $alumno)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-800 uppercase">{{ $alumno->user->name }}</td>
                                            <td class="px-6 py-4 font-mono text-gray-400 text-xs">{{ $alumno->dni }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 rounded-lg font-black text-xs {{ ($alumno->puntaje_sort ?? 0) <= 0 ? 'bg-gray-100 text-gray-400' : 'bg-emerald-100 text-emerald-700' }}">
                                                    {{ number_format($alumno->puntaje_sort ?? 0, 3) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button wire:click="setView('show', {{ $alumno->user_id }})" class="p-1 hover:bg-indigo-100 rounded-lg transition-colors">
                                                        <img src="{{ asset('meta-ver/puntaje.jpeg') }}" alt="Ver" class="w-10 h-10 object-contain">
                                                    </button>
                                                    <button wire:click="setView('edit', {{ $alumno->user_id }})" class="p-1 hover:bg-amber-100 rounded-lg transition-colors">
                                                        <img src="{{ asset('meta-editar/puntaje.jpeg') }}" alt="Editar" class="w-10 h-10 object-contain">
                                                    </button>
                                                    <button wire:click="confirmarEliminacion({{ $alumno->user_id }})" class="p-1 hover:bg-rose-100 rounded-lg transition-colors">
                                                        <img src="{{ asset('meta-eliminar/ptje.jpeg') }}" alt="Limpiar" class="w-10 h-10 object-contain">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="p-12 text-center text-gray-400 font-bold italic">No se seleccionó simulacro o no hay resultados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $alumnos->links() }}</div>
                    @else
                        <div class="py-20 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-50 rounded-full mb-4">
                                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Seleccione Área, Ciclo y Simulacro para gestionar puntajes</p>
                        </div>
                    @endif

                @elseif($view === 'edit')
                    <!-- FORMULARIO DE EDICIÓN -->
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-gray-900 text-white p-8 rounded-2xl mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div>
                                <h2 class="text-2xl font-black uppercase italic">{{ $selectedAlumno->user->name }}</h2>
                                <p class="text-indigo-400 font-bold text-xs tracking-widest uppercase mt-1">DNI: {{ $selectedAlumno->dni }}</p>
                            </div>
                            <div class="text-center md:text-right">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Puntaje Final</span>
                                <span class="text-4xl font-black text-[#98FB98]">{{ number_format($puntaje, 3) }}</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-emerald-600 uppercase ml-2 tracking-widest">Correctas</label>
                                <input type="number" wire:model.live.debounce.500ms="correctas" class="w-full text-2xl font-black text-center bg-emerald-50 border-2 border-emerald-100 rounded-2xl p-4 focus:border-emerald-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-rose-600 uppercase ml-2 tracking-widest">Incorrectas</label>
                                <input type="number" wire:model.live.debounce.500ms="incorrectas" class="w-full text-2xl font-black text-center bg-rose-50 border-2 border-rose-100 rounded-2xl p-4 focus:border-rose-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase ml-2 tracking-widest">En Blanco</label>
                                <input type="number" wire:model.live.debounce.500ms="blanco" class="w-full text-2xl font-black text-center bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 focus:border-gray-500 outline-none transition-all">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-4 border-t pt-8">
                            @if(session()->has('error_suma'))
                                <div class="flex items-center px-4 py-2 bg-rose-100 text-rose-600 text-[10px] font-black rounded-xl uppercase animate-pulse">
                                    {{ session('error_suma') }}
                                </div>
                            @endif
                            <button wire:click="setView('list')" class="px-8 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-xs hover:bg-gray-200 transition-all">Cancelar</button>
                            <button wire:click="save" @if($error_suma) disabled @endif class="h-14 px-10 bg-[#98FB98] text-black font-black rounded-xl uppercase text-xs tracking-widest shadow-xl hover:bg-[#7FE67F] transition-all disabled:opacity-30">Guardar Cambios</button>
                        </div>
                    </div>

                @elseif($view === 'show')
                    <!-- VISTA DETALLE -->
                    <div class="max-w-2xl mx-auto py-8">
                        <div class="text-center mb-10">
                            <h2 class="text-3xl font-black text-gray-800 uppercase leading-tight">{{ $selectedAlumno->user->name }}</h2>
                            <p class="text-indigo-500 font-bold tracking-widest text-xs uppercase mt-2">DNI: {{ $selectedAlumno->dni }}</p>
                        </div>

                        <div class="grid grid-cols-3 gap-6 mb-10">
                            <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-3xl text-center">
                                <span class="block text-[10px] font-black text-emerald-600 uppercase mb-2">Correctas</span>
                                <span class="text-3xl font-black text-emerald-700">{{ $correctas }}</span>
                            </div>
                            <div class="bg-rose-50 border border-rose-100 p-6 rounded-3xl text-center">
                                <span class="block text-[10px] font-black text-rose-600 uppercase mb-2">Incorrectas</span>
                                <span class="text-3xl font-black text-rose-700">{{ $incorrectas }}</span>
                            </div>
                            <div class="bg-gray-50 border border-gray-100 p-6 rounded-3xl text-center">
                                <span class="block text-[10px] font-black text-gray-500 uppercase mb-2">Blancos</span>
                                <span class="text-3xl font-black text-gray-700">{{ $blanco }}</span>
                            </div>
                        </div>

                        <div class="bg-indigo-600 p-10 rounded-[3rem] text-center shadow-2xl shadow-indigo-200 relative overflow-hidden">
                            <div class="relative z-10">
                                <span class="block text-indigo-200 text-xs font-bold uppercase tracking-[0.3em] mb-2">Puntaje Obtenido</span>
                                <span class="text-6xl font-black text-white">{{ number_format($puntaje, 3) }}</span>
                            </div>
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full"></div>
                        </div>
                    </div>
                @endif
            @endhasanyrole

            @hasrole('alumno')
                <!-- HISTORIAL ALUMNO -->
                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                            <tr>
                                <th class="px-6 py-5">Simulacro</th>
                                <th class="px-6 py-5 text-center">Correctas</th>
                                <th class="px-6 py-5 text-center">Incorrectas</th>
                                <th class="px-6 py-5 text-center">Puntaje</th>
                                <th class="px-6 py-5 text-center">Ranking</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($misResultados as $res)
                                <tr class="hover:bg-amber-50/50 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="font-black text-gray-800 uppercase leading-none">{{ $res->simulacro->nombre }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold mt-1">{{ $res->simulacro->fecha->format('d M, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center font-bold text-emerald-600 bg-emerald-50/20">{{ $res->correctas }}</td>
                                    <td class="px-6 py-5 text-center font-bold text-rose-600 bg-rose-50/20">{{ $res->incorrectas }}</td>
                                    <td class="px-6 py-5 text-center font-black text-indigo-600 text-lg">{{ number_format($res->puntaje, 3) }}</td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="px-4 py-1.5 bg-amber-100 text-amber-700 rounded-full font-black text-xs shadow-sm"># {{ $res->puesto }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endhasrole
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl z-50 max-w-sm w-full p-8 animate-fade-in-up border border-gray-100">
                <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-rose-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight mb-2">¿Limpiar Puntaje?</h3>
                <p class="text-gray-500 text-sm mb-8 font-medium italic">Los datos volverán a cero de forma inmediata. Esta acción no se puede deshacer.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-gray-50 text-gray-400 rounded-xl font-bold uppercase text-[10px]">Cancelar</button>
                    <button wire:click="deletePuntaje" @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-rose-600 text-white rounded-xl font-black uppercase text-[10px] shadow-lg shadow-rose-100 transition-all active:scale-95">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>