<div class="bg-amber-50/40 min-h-screen font-sans antialiased pb-20" x-data="{ showDeleteModal: false }" x-on:show-delete-modal.window="showDeleteModal = true">
    <div class="w-full px-4 pt-6">

        <!-- 1. BREADCRUMBS DINÁMICOS -->
        <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-2xl" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <button wire:click="setView('list')" class="inline-flex items-center text-sm font-medium hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Gestión de Puntajes
                    </button>
                </li>
                @if($view === 'edit')
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-bold text-indigo-600 md:ml-2">Registrar Nota: {{ $selectedAlumno->user->name }}</span>
                        </div>
                    </li>
                @endif
                @if($view === 'show')
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-bold text-indigo-600 md:ml-2">Detalle de Resultado</span>
                        </div>
                    </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES GLOBAL -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl flex justify-between items-center animate-bounce">
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        @hasanyrole('admin|docente')
            {{-- A. VISTA LISTADO (TABLA + BUSCADOR + ORDEN) --}}
            @if($view === 'list')
                <div class="space-y-6 animate-fade-in">
                    <!-- FILTROS -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Área</label>
                            <select wire:model.live="area_id" class="w-full bg-gray-50 border-none rounded-xl font-bold text-sm p-3.5 focus:ring-2 focus:ring-indigo-500">
                                <option value="">Seleccione Área...</option>
                                @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ciclo</label>
                            <select wire:model.live="ciclo_id" class="w-full bg-gray-50 border-none rounded-xl font-bold text-sm p-3.5 disabled:opacity-40" {{ !$area_id ? 'disabled' : '' }}>
                                <option value="">Seleccione Ciclo...</option>
                                @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Simulacro</label>
                            <select wire:model.live="simulacro_id" class="w-full bg-gray-50 border-none rounded-xl font-bold text-sm p-3.5 disabled:opacity-40" {{ !$ciclo_id ? 'disabled' : '' }}>
                                <option value="">Seleccione Simulacro...</option>
                                @foreach($simulacros as $sim) <option value="{{ $sim->id }}">{{ $sim->nombre }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    @if($simulacro_id)
                        <!-- BUSCADOR Y ORDEN -->
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-indigo-900 p-4 rounded-2xl shadow-lg">
                            <div class="relative w-full md:w-1/2">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-indigo-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por Nombre o DNI..." class="w-full bg-indigo-800 border-none text-white placeholder-indigo-300 rounded-xl py-2.5 pl-10 focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <button wire:click="toggleSort" class="flex items-center gap-2 px-6 py-2.5 bg-white text-indigo-900 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-50 transition-all">
                                <span>Ordenar Puntaje: {{ $sortScore === 'desc' ? 'MAYOR A MENOR' : 'MENOR A MAYOR' }}</span>
                                <svg class="w-4 h-4 {{ $sortScore === 'asc' ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <!-- TABLA -->
                        <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 border-b border-gray-100 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                    <tr>
                                        <th class="px-6 py-4">Estudiante</th>
                                        <th class="px-6 py-4">DNI</th>
                                        <th class="px-6 py-4 text-center">Puntaje</th>
                                        <th class="px-6 py-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($alumnos as $alumno)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-800 uppercase">{{ $alumno->user->name }}</td>
                                            <td class="px-6 py-4 font-mono text-gray-500 text-xs">{{ $alumno->dni }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-4 py-1 rounded-lg font-black {{ ($alumno->puntaje_sort ?? 0) <= 0 ? 'bg-gray-100 text-gray-400' : 'bg-emerald-100 text-emerald-700' }}">
                                                    {{ number_format($alumno->puntaje_sort ?? 0, 3) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-1">
                                                <button wire:click="setView('show', {{ $alumno->user_id }})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver Detalle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <button wire:click="setView('edit', {{ $alumno->user_id }})" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="Editar Puntaje">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button wire:click="confirmarEliminacion({{ $alumno->user_id }})" class="p-2 text-rose-600 hover:bg-rose-100 rounded-lg transition-colors" title="Limpiar Puntaje">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="p-10 text-center text-gray-400 font-bold italic">No se encontraron resultados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="p-4 bg-gray-50">{{ $alumnos->links() }}</div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- B. VISTA REGISTRO (FORMULARIO INDIVIDUAL) --}}
            @if($view === 'edit')
                <div class="max-w-4xl mx-auto animate-fade-in-down">
                    <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">
                        <div class="p-8 bg-indigo-900 text-white">
                            <h2 class="text-2xl font-black uppercase italic">{{ $selectedAlumno->user->name }}</h2>
                            <p class="text-indigo-300 font-bold text-xs tracking-widest mt-1 uppercase">DNI: {{ $selectedAlumno->dni }} | Registro de Notas</p>
                        </div>
                        
                        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-emerald-600 uppercase ml-2 tracking-widest">Correctas</label>
                                <input type="number" wire:model.live.debounce.500ms="correctas" class="w-full text-2xl font-black text-center bg-emerald-50 border-2 border-emerald-100 rounded-2xl p-4 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-rose-600 uppercase ml-2 tracking-widest">Incorrectas</label>
                                <input type="number" wire:model.live.debounce.500ms="incorrectas" class="w-full text-2xl font-black text-center bg-rose-50 border-2 border-rose-100 rounded-2xl p-4 focus:ring-rose-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase ml-2 tracking-widest">En Blanco</label>
                                <input type="number" wire:model.live.debounce.500ms="blanco" class="w-full text-2xl font-black text-center bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 focus:ring-gray-400">
                            </div>
                        </div>

                        <div class="px-8 pb-8 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="text-center md:text-left">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Puntaje Calculado</span>
                                <span class="text-5xl font-black text-indigo-600">{{ number_format($puntaje, 3) }}</span>
                            </div>

                            <div class="flex flex-col items-end gap-3">
                                @if(session()->has('error_suma'))
                                    <span class="px-4 py-2 bg-rose-100 text-rose-600 text-xs font-black rounded-lg animate-pulse">
                                        {{ session('error_suma') }}
                                    </span>
                                @else
                                    <span class="px-4 py-2 bg-emerald-100 text-emerald-600 text-xs font-black rounded-lg">
                                        ✓ Suma perfecta: 100/100
                                    </span>
                                @endif
                                
                                <div class="flex gap-2">
                                    <button wire:click="setView('list')" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold uppercase text-xs hover:bg-gray-200 transition-all">Cancelar</button>
                                    <button wire:click="save" @if($error_suma) disabled @endif class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-indigo-700 disabled:opacity-30 transition-all">Guardar Puntaje</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- C. VISTA DETALLE (SOLO LECTURA) --}}
            @if($view === 'show')
                <div class="max-w-2xl mx-auto animate-fade-in">
                    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h2 class="text-2xl font-black text-gray-800 uppercase leading-tight">{{ $selectedAlumno->user->name }}</h2>
                                <p class="text-indigo-500 font-bold tracking-widest text-xs uppercase">{{ $selectedAlumno->dni }}</p>
                            </div>
                            <button wire:click="setView('list')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="bg-emerald-50 p-4 rounded-2xl text-center">
                                <span class="block text-[10px] font-black text-emerald-600 uppercase">Correctas</span>
                                <span class="text-2xl font-black text-emerald-700">{{ $correctas }}</span>
                            </div>
                            <div class="bg-rose-50 p-4 rounded-2xl text-center">
                                <span class="block text-[10px] font-black text-rose-600 uppercase">Incorrectas</span>
                                <span class="text-2xl font-black text-rose-700">{{ $incorrectas }}</span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl text-center">
                                <span class="block text-[10px] font-black text-gray-500 uppercase">Blancos</span>
                                <span class="text-2xl font-black text-gray-700">{{ $blanco }}</span>
                            </div>
                        </div>

                        <div class="bg-indigo-600 p-6 rounded-[2rem] text-center shadow-lg">
                            <span class="block text-indigo-200 text-xs font-bold uppercase tracking-[0.2em] mb-1">Puntaje Final</span>
                            <span class="text-5xl font-black text-white">{{ number_format($puntaje, 3) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endhasanyrole

        {{-- D. VISTA ALUMNO (HISTORIAL) --}}
        @hasrole('alumno')
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-fade-in">
                <div class="p-8 bg-gray-900">
                    <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Mi Historial de Simulacros</h3>
                    <p class="text-gray-400 text-xs uppercase font-bold mt-1">Sigue tu progreso académico</p>
                </div>
                <div class="overflow-x-auto">
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
                                    <td class="px-6 py-5 text-center font-bold text-emerald-600 bg-emerald-50/30">{{ $res->correctas }}</td>
                                    <td class="px-6 py-5 text-center font-bold text-rose-600 bg-rose-50/30">{{ $res->incorrectas }}</td>
                                    <td class="px-6 py-5 text-center font-black text-indigo-600 text-lg">{{ number_format($res->puntaje, 3) }}</td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="px-4 py-1.5 bg-amber-100 text-amber-700 rounded-full font-black text-xs shadow-sm">
                                            # {{ $res->puesto }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endhasrole

    </div>

    <!-- MODAL DE CONFIRMACIÓN (ALUMNO ESPECÍFICO) -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 opacity-60"></div>
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-2xl z-50 max-w-sm w-full p-8 text-center animate-fade-in-up">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 uppercase mb-2">¿Limpiar Puntaje?</h3>
                <p class="text-gray-500 text-sm mb-8 font-medium italic">Los datos de este alumno para este simulacro volverán a cero. Esta acción es inmediata.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-xs">Cancelar</button>
                    <button wire:click="deletePuntaje" @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-rose-600 text-white rounded-xl font-black uppercase text-xs shadow-lg shadow-rose-200">Sí, Limpiar</button>
                </div>
            </div>
        </div>
    </div>
</div>