<div class="py-8 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS -->
        <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 00-1.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <button wire:click="closeModal" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">Matrículas</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'create' ? 'Nuevo Registro' : ($view == 'edit' ? 'Editando' : 'Detalles') }}
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
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">&times;</button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 shadow-sm rounded-r-xl">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all">

            @if($view == 'index')
                <!-- VISTA: LISTADO (INDEX) -->
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 focus:bg-white transition-all outline-none" 
                                   placeholder="Buscar alumno o DNI...">
                        </div>
                        
                        <button wire:click="create" 
                                class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center justify-center active:scale-95">
                            <div class="relative inline-flex items-center justify-center w-6 h-6 mr-2 flex-shrink-0">
                                <img src="{{ asset('metaforas/MATRICULA.svg') }}?v={{ time() }}" class="w-full h-full object-contain">
                                <svg class="w-3.5 h-3.5 absolute -bottom-1 -right-1 bg-white rounded-full p-[1px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="#3B82F6" stroke="#0F172A" stroke-width="2.5" /><path d="M12 6v12M6 12h12" stroke="#0F172A" stroke-width="3" stroke-linecap="round" /></svg>
                            </div>
                            Nueva Matrícula
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Alumno</th>
                                    <th class="px-6 py-4">Ciclo / Carrera</th>
                                    <th class="px-6 py-4 text-center">Modalidad</th>
                                    <th class="px-6 py-4">Monto Total</th>
                                    <th class="px-6 py-4 text-center">Estado</th>
                                    <th class="px-6 py-4 text-right">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($matriculas as $m)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-9 w-9 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-xs mr-3">
                                                    {{ strtoupper(substr($m->alumno->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900 leading-tight group-hover:text-indigo-700 transition-colors">{{ $m->alumno->user->name }}</div>
                                                    <div class="text-[11px] text-gray-400 font-medium">DNI: {{ $m->alumno->dni }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-semibold text-gray-600">{{ $m->ciclo->nombre ?? 'N/A' }}</div>
                                            <div class="text-[10px] text-indigo-400 font-bold uppercase tracking-tighter">{{ $m->carrera->nombre ?? 'Sin Carrera' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg bg-blue-50 text-blue-600 border border-blue-100 italic">{{ $m->modalidad }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-sm font-bold text-gray-800">S/ {{ number_format($m->monto_total, 2) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight {{ $m->estado == 'Activa' ? 'bg-green-100 text-green-700' : ($m->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $m->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-2">
                                                <button wire:click="show({{ $m->id }})" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition relative inline-flex items-center justify-center flex-shrink-0" title="Ver Detalles">
                                                    <img src="{{ asset('metaforas/MATRICULA.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                    <svg class="w-4 h-4 absolute bottom-1.5 right-1.5 bg-white text-black rounded-full shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <button wire:click="edit({{ $m->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition relative inline-flex items-center justify-center flex-shrink-0" title="Editar">
                                                    <img src="{{ asset('metaforas/MATRICULA.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                    <svg class="w-[22px] h-[22px] absolute bottom-1 right-1 bg-white rounded-full p-[3px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g transform="rotate(45 12 12)" stroke="#1F2937" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"><path fill="#F87171" d="M9 5 C9 2, 15 2, 15 5 L15 7 L9 7 Z" /><rect fill="#E5E7EB" x="9" y="7" width="6" height="3" /><rect fill="#FACC15" x="9" y="10" width="6" height="7" /><line x1="12" y1="10" x2="12" y2="17" stroke="#EAB308" stroke-width="1.5" /><polygon fill="#FEF08A" points="9,17 15,17 12,21" /><polygon fill="#1F2937" points="11.25,20 12.75,20 12,21" /></g></svg>
                                                </button>
                                                <button wire:click="confirmDelete({{ $m->id }})" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition relative inline-flex items-center justify-center flex-shrink-0" title="Eliminar">
                                                    <img src="{{ asset('metaforas/MATRICULA.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                    <svg class="w-[22px] h-[22px] absolute bottom-1 right-1 bg-white rounded-full p-[2px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5V3.5C9 3 9.5 2.5 10 2.5h4c.5 0 1 .5 1 1.5V5" fill="none" /><rect x="4" y="5" width="16" height="3" rx="1" fill="#FCA5A5" /><path d="M5.5 8l1.5 13.5c.1.8.8 1.5 1.5 1.5h7c.8 0 1.4-.7 1.5-1.5L18.5 8" fill="#FEE2E2" /><line x1="8.5" y1="11" x2="9.5" y2="18" /><line x1="12" y1="11" x2="12" y2="18" /><line x1="15.5" y1="11" x2="14.5" y2="18" /><circle cx="17" cy="17" r="6" fill="#FCA5A5" /><path d="M14.5 14.5l5 5m0-5l-5 5" /></g></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic text-sm">No se encontraron matrículas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 px-4 py-2">{{ $matriculas->links() }}</div>
                </div>

            @elseif($view == 'create' || $view == 'edit')
                <!-- VISTA: FORMULARIO (CREAR/EDITAR) -->
                <div class="p-8 max-w-5xl mx-auto animate-fade-in">
                    <form wire:submit.prevent="save" class="space-y-10">
                        
                        <!-- 1. SELECCIÓN DE ALUMNO -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border-2 border-dashed border-gray-100">
                            <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> 1. Información del Estudiante
                            </h3>
                            <div class="max-w-xl">
                                @if(!$alumno_id)
                                    <div class="relative group">
                                        <input type="text" wire:model.live="search" placeholder="Escriba Nombre o DNI del alumno..." 
                                               class="w-full p-4 bg-white border-2 border-gray-50 rounded-2xl shadow-sm focus:border-indigo-500 transition-all outline-none">
                                        @if(count($alumnos_busqueda) > 0)
                                            <ul class="absolute z-50 mt-2 w-full bg-white shadow-2xl rounded-2xl border border-gray-100 py-3 max-h-64 overflow-y-auto custom-scrollbar">
                                                @foreach($alumnos_busqueda as $alumno)
                                                    <li wire:click="selectAlumno({{ $alumno->user_id }})" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer transition flex items-center justify-between group/item">
                                                        <div>
                                                            <div class="font-bold text-gray-800 group-hover/item:text-indigo-700">{{ $alumno->user->name }}</div>
                                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">DNI: {{ $alumno->dni }}</div>
                                                        </div>
                                                        <svg class="w-4 h-4 text-indigo-400 opacity-0 group-hover/item:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-between p-5 bg-indigo-600 rounded-2xl text-white shadow-xl shadow-indigo-100 animate-fade-in">
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center font-black mr-4 text-xl">
                                                {{ strtoupper(substr($nombre_alumno, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-lg leading-tight">{{ $nombre_alumno }}</div>
                                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-80">Alumno Seleccionado</div>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('alumno_id', null)" class="p-2 hover:bg-white/10 rounded-xl transition group" title="Cambiar Alumno">
                                            <svg class="w-6 h-6 text-white/50 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endif
                                @error('alumno_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- 2. CONFIGURACIÓN ACADÉMICA -->
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> 2. Ubicación Académica
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Área de Estudios</label>
                                    <select wire:model.live="area_id" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer">
                                        <option value="">Seleccione Área...</option>
                                        @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                                    </select>
                                    @error('area_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Ciclo Académico</label>
                                    <select wire:model="ciclo_id" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer disabled:opacity-50" {{ !$area_id ? 'disabled' : '' }}>
                                        <option value="">Seleccione Ciclo...</option>
                                        @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                                    </select>
                                    @error('ciclo_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Carrera Destino</label>
                                    <select wire:model="carrera_id" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer disabled:opacity-50" {{ !$area_id ? 'disabled' : '' }}>
                                        <option value="">Seleccione Carrera...</option>
                                        @foreach($carreras as $carrera) <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option> @endforeach
                                    </select>
                                    @error('carrera_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 3. CONFIGURACIÓN FINANCIERA -->
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> 3. Plan de Inversión
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Monto Total</label>
                                    <div class="relative group">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">S/</span>
                                        <input type="number" step="0.01" wire:model.live="monto_total" class="w-full pl-10 p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700">
                                    </div>
                                    @error('monto_total') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Modalidad de Pago</label>
                                    <select wire:model.live="modalidad" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer">
                                        <option value="Pago Unico">Pago Único (1 Cuota)</option>
                                        <option value="2 Cuotas">Dividido en 2 Cuotas</option>
                                        <option value="3 Cuotas">Dividido en 3 Cuotas</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Estado</label>
                                    <select wire:model="estado" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer">
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Activa">Activa / Regular</option>
                                        <option value="Anulada">Anulada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4. CRONOGRAMA -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Desglose de Cuotas Generadas
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                @foreach($cuotas as $index => $cuota)
                                <div class="bg-white p-6 rounded-3xl border-2 border-gray-50 shadow-sm relative group hover:border-indigo-100 transition-colors">
                                    <span class="absolute -top-3 left-6 bg-indigo-600 text-white px-3 py-0.5 text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-indigo-100 italic">Cuota #{{ $index }}</span>
                                    
                                    <div class="space-y-4 pt-2">
                                        <div>
                                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Monto Cuota</label>
                                            <input type="number" wire:model="cuotas.{{ $index }}.monto" class="mt-1 w-full p-2.5 bg-gray-50 border-gray-100 rounded-xl text-sm font-bold outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Vencimiento</label>
                                            <input type="date" wire:model="cuotas.{{ $index }}.fecha_vencimiento" class="mt-1 w-full p-2.5 bg-gray-50 border-gray-100 rounded-xl text-sm font-bold outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. BOTONES -->
                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-10 border-t border-gray-100">
                            <button type="button" wire:click="closeModal" class="px-8 py-3.5 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">Cancelar</button>
                            <button type="submit" class="px-10 py-3.5 bg-indigo-600 text-white rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition active:scale-95 flex items-center justify-center">
                                {{ $view == 'create' ? 'Procesar Matrícula' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>

            @elseif($view == 'show' && $viewingMatricula)
                <!-- VISTA: DETALLES (SHOW) -->
                <div class="p-8 animate-fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                                <h4 class="text-indigo-200 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Alumno Matriculado</h4>
                                <p class="text-3xl font-black leading-tight">{{ $viewingMatricula->alumno->user->name }}</p>
                                <p class="text-indigo-100 opacity-80 text-sm mt-2 font-medium">DNI: {{ $viewingMatricula->alumno->dni }}</p>
                                
                                <div class="mt-8 pt-8 border-t border-white/10">
                                    <p class="text-indigo-200 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Información Académica</p>
                                    <p class="text-sm font-bold">{{ $viewingMatricula->ciclo->nombre ?? 'N/A' }}</p>
                                    <p class="text-[11px] text-indigo-100 italic mt-1">{{ $viewingMatricula->carrera->nombre ?? 'Sin Carrera' }}</p>
                                    <p class="text-[11px] text-indigo-100 opacity-60 uppercase font-black mt-2 tracking-widest">{{ $viewingMatricula->ciclo->area->nombre ?? 'S/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-3xl p-8 border-2 border-gray-50 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span> Estado Financiero
                                </h4>
                                <div class="space-y-5">
                                    <div class="flex justify-between items-end border-b border-gray-50 pb-3">
                                        <span class="text-xs font-bold text-gray-400">Costo Total:</span>
                                        <span class="text-lg font-black text-gray-800 font-mono">S/ {{ number_format($viewingMatricula->monto_total, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-400">Situación:</span>
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $viewingMatricula->estado == 'Activa' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $viewingMatricula->estado }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <h3 class="text-xl font-black text-gray-800 tracking-tight flex items-center">
                                <svg class="w-6 h-6 mr-3 text-indigo-600 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Cronograma de Pagos
                            </h3>
                            <div class="grid grid-cols-1 gap-5">
                                @foreach($viewingMatricula->pagos as $pago)
                                <div class="flex items-center justify-between p-6 bg-white border-2 border-gray-50 rounded-3xl hover:border-indigo-100 transition-all group">
                                    <div class="flex items-center">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex flex-col items-center justify-center mr-5 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                            <span class="text-[10px] font-black uppercase tracking-tighter opacity-50 group-hover:opacity-80">Cuota</span>
                                            <span class="text-xl font-black leading-none">{{ $pago->numero_cuota }}</span>
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-800 text-base">S/ {{ number_format($pago->monto, 2) }}</p>
                                            <p class="text-[11px] text-gray-400 font-bold flex items-center mt-1 uppercase tracking-tighter">Vence: {{ \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border-2 {{ $pago->estado == 'Pagado' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-red-50 border-red-100 text-red-500' }}">
                                            {{ $pago->estado }}
                                        </span>
                                    </div>
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
    @if($matriculaIdBeingDeleted)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('matriculaIdBeingDeleted', null)"></div>
        <div class="relative bg-white rounded-[2rem] max-w-lg w-full shadow-2xl transform transition-all border border-gray-100 z-[110] overflow-hidden">
            <div class="p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-red-50 border border-red-100 text-red-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Eliminar Matrícula?</h3>
                        <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">
                            Se eliminará el registro académico y el cronograma de pagos asociado. Esta operación es irreversible.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-4 flex flex-col sm:flex-row-reverse gap-3 mt-2">
                <button wire:click="delete" type="button" class="inline-flex justify-center rounded-xl px-8 py-3 bg-red-600 text-sm font-black text-white hover:bg-red-700 shadow-lg shadow-red-100 transition-all active:scale-95">
                    Eliminar Registro
                </button>
                <button wire:click="$set('matriculaIdBeingDeleted', null)" type="button" class="inline-flex justify-center rounded-xl border border-gray-200 px-8 py-3 bg-white text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">
                    Descartar
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Útil para debug: Muestra si hay fallos de validación que no ves -->
    @if ($errors->any())
        <div class="fixed bottom-4 right-4 z-50 max-w-xs animate-fade-in">
            <div class="p-4 bg-orange-50 border-l-4 border-orange-500 text-orange-800 shadow-xl rounded-r-xl">
                <p class="font-black text-[10px] uppercase tracking-widest mb-2">Errores de Validación:</p>
                <ul class="text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>