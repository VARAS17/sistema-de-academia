<div class="py-8 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative" x-data="{ tab: @entangle('tab') }">
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS (Consistente) -->
        <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl">
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
                        <button wire:click="cancel" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2">Docentes</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'create' ? 'Registro' : ($view == 'edit' ? 'Edición' : 'Ficha') }}
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ESTADO -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
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
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none" 
                                   placeholder="Buscar docente o DNI...">
                        </div>
                        
                        <button wire:click="create"
                            class="h-14 px-6 flex items-centero px-6 py-2.5 bg-[#98FB98] text-black font-bold rounded-xl hover:bg-[#7FE67F] transition shadow-lg flex items-center justify-center active:scale-95">

                            <img src="{{ asset('meta-register/docente.png') }}"
                                alt="Registrar Docente"
                                class="w-14 h-14 mr-2 object-contain">

                            Registrar Docente
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Docente</th>
                                    <th class="px-6 py-4">Especialidad</th>
                                    <th class="px-6 py-4">DNI</th>
                                    <th class="px-6 py-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($docentes as $docente)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-xs mr-3 border-2 border-white shadow-sm">
                                                    {{ strtoupper(substr($docente->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $docente->user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-white text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-tight border border-gray-100">
                                                {{ $docente->especialidad }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $docente->dni }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-1">
                                                <button wire:click="show({{ $docente->user_id }})"
                                                    class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors"
                                                    title="Ver Detalles">
                                                    <img src="{{ asset('meta-ver/docente.jpeg') }}"
                                                        alt="Ver"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                                <button wire:click="edit({{ $docente->user_id }})"
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                    title="Editar">
                                                    <img src="{{ asset('meta-editar/docente.jpeg') }}"
                                                        alt="Editar"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                                <button wire:click="confirmDelete({{ $docente->user_id }})"
                                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Eliminar">
                                                    <img src="{{ asset('meta-eliminar/docente.jpeg') }}"
                                                        alt="Eliminar"
                                                        class="w-12 h-12 object-contain">
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">No hay resultados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $docentes->links() }}</div>
                </div>

            <!-- VISTA: CREATE / EDIT -->
            @elseif($view == 'create' || $view == 'edit')
                <div class="p-0">
                    <div class="flex border-b border-gray-100 bg-gray-50/50">
                        <button type="button" wire:click="setTab(1)" :class="tab === 1 ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-gray-400'" class="px-8 py-4 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all">
                            1. Perfil del Docente
                        </button>
                        <button type="button" wire:click="setTab(2)" :class="tab === 2 ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-gray-400'" class="px-8 py-4 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all">
                            2. Asignación Académica
                        </button>
                    </div>

                    <form wire:submit.prevent="store" class="p-8">
                        
                        <!-- TAB 1: DATOS PERSONALES -->
                        <div x-show="tab === 1" x-transition class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-2">Datos de Identidad</h4>
                                    <div class="space-y-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-black text-gray-500 uppercase ml-1">Nombre Completo</label>
                                            <input wire:model="name" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none text-sm">
                                            @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-[10px] font-black text-gray-500 uppercase ml-1">DNI (8 dígitos)</label>
                                                <input wire:model="dni" type="text" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 transition-all outline-none text-sm font-mono">
                                                @error('dni') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[10px] font-black text-gray-500 uppercase ml-1">Teléfono (Inicia 9)</label>
                                                <input wire:model="telefono" type="text" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 transition-all outline-none text-sm font-mono">
                                                @error('telefono') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-2">Información Profesional</h4>
                                    <div class="space-y-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-black text-gray-500 uppercase ml-1">Especialidad</label>
                                            <input wire:model="especialidad" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 transition-all outline-none text-sm">
                                            @error('especialidad') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-black text-gray-500 uppercase ml-1">Fecha Contratación</label>
                                            <input wire:model="fecha_contratacion" type="date" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 transition-all outline-none text-sm">
                                            @error('fecha_contratacion') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <button type="button" wire:click="goToStepTwo" class="px-8 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-100 transition-all">Siguiente Paso: Carga Académica &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 2: CARGA ACADÉMICA -->
                        <div x-show="tab === 2" x-transition class="space-y-6">
                            <div class="bg-indigo-600 p-4 rounded-xl shadow-lg mb-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-white/20 p-2 rounded-lg mr-4">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-100 uppercase tracking-widest">Asignando cursos para:</p>
                                        <p class="text-sm font-black text-white uppercase">{{ $name ?: 'Nuevo Docente' }}</p>
                                    </div>
                                </div>
                            </div>

                            @error('selectedCursos') <p class="text-red-500 text-[10px] font-bold mb-4">{{ $message }}</p> @enderror

                            <div class="space-y-4">
                                @foreach($allCursosGrouped as $cicloNombre => $areas)
                                    <div x-data="{ open: false }" class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                                        <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors">
                                            <span class="text-sm font-black text-gray-700 uppercase tracking-tight">{{ $cicloNombre }}</span>
                                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                                        </button>

                                        <div x-show="open" x-transition class="p-6 bg-gray-50/30 border-t border-gray-50 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                            @foreach($areas as $areaNombre => $cursos)
                                                <div class="space-y-3">
                                                    <h5 class="text-[10px] font-black text-indigo-500 uppercase border-b border-indigo-100 pb-1">{{ $areaNombre }}</h5>
                                                    <div class="space-y-2">
                                                        @foreach($cursos as $curso)
                                                            <label class="flex items-center cursor-pointer group">
                                                                <input type="checkbox" wire:model="selectedCursos" value="{{ $curso->id }}" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                <span class="ml-2 text-xs font-bold text-gray-600 group-hover:text-indigo-600 transition-colors">{{ $curso->nombre }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between pt-6 border-t">
                                <button type="button" wire:click="setTab(1)" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-600 transition-colors">&larr; Volver al perfil</button>
                                <div class="flex gap-4">
                                    <button type="button" wire:click="cancel" class="px-8 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Descartar</button>
                                    <button type="submit" class="px-12 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 flex items-center active:scale-95 transition-all">
                                        <span wire:loading class="mr-3 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                        {{ $user_id ? 'Actualizar Docente' : 'Finalizar Registro' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            <!-- VISTA: SHOW -->
            @elseif($view == 'show')
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between border-b border-gray-100 pb-8 mb-8 gap-6">
                        <div class="flex items-center space-x-6">
                            <div class="h-20 w-20 bg-indigo-600 rounded-3xl flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-indigo-100">
                                {{ strtoupper(substr($selectedDocente->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">Perfil Profesional</span>
                                <h3 class="text-3xl font-black text-gray-800 leading-none">{{ $selectedDocente->user->name }}</h3>
                                <p class="text-gray-400 font-bold mt-2 flex items-center text-sm">
                                    DNI: {{ $selectedDocente->dni }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="cancel" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-sm active:scale-95">Volver al Listado</button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 h-fit">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 text-center">Detalles Personales</h4>
                            <div class="space-y-4">
                                <div><p class="text-[9px] font-black text-indigo-400 uppercase">DNI</p><p class="text-sm font-bold text-gray-800">{{ $selectedDocente->dni }}</p></div>
                                <div><p class="text-[9px] font-black text-indigo-400 uppercase">Especialidad</p><p class="text-sm font-bold text-gray-800">{{ $selectedDocente->especialidad }}</p></div>
                                <div><p class="text-[9px] font-black text-indigo-400 uppercase">Teléfono</p><p class="text-sm font-bold text-gray-800">{{ $selectedDocente->telefono }}</p></div>
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                             <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Cursos Activos</h4>
                             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($selectedDocente->cursos as $curso)
                                    <div class="bg-white border border-gray-100 p-4 rounded-xl shadow-sm">
                                        <p class="font-black text-gray-800 text-sm mb-1">{{ $curso->nombre }}</p>
                                        <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest">{{ $curso->area->nombre }} | {{ $curso->ciclo->nombre }}</p>
                                    </div>
                                @endforeach
                             </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL DE ELIMINACIÓN -->
    @if($docenteIdBeingDeleted)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl transform transition-all border border-gray-100 z-[110] overflow-hidden">
                <div class="p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-6">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Eliminar Docente?</h3>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Esta acción no se puede deshacer.</p>
                </div>
                <div class="bg-gray-50 px-8 py-4 flex flex-col sm:flex-row-reverse gap-3">
                    <button wire:click="delete" class="w-full sm:w-auto px-8 py-3 bg-red-600 text-sm font-black text-white rounded-xl hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-100">Confirmar</button>
                    <button wire:click="cancelDelete" class="w-full sm:w-auto px-8 py-3 bg-white border border-gray-200 text-sm font-bold text-gray-600 rounded-xl hover:bg-gray-50 transition-all">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>