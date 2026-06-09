<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS (Consistencia HCI) -->
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

        <!-- 2. MENSAJES DE ÉXITO -->
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
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nueva Matrícula
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Alumno</th>
                                    <th class="px-6 py-4">Ciclo</th>
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
                                        <td class="px-6 py-4 font-semibold text-gray-600 text-xs">{{ $m->ciclo->nombre ?? 'N/A' }}</td>
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
                                                <button wire:click="show({{ $m->id }})" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Ver Detalles">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                <button wire:click="edit({{ $m->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button wire:click="delete({{ $m->id }})" wire:confirm="¿Deseas eliminar permanentemente esta matrícula?" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <p class="text-gray-400 font-medium italic text-sm">No se encontraron matrículas registradas.</p>
                                            </div>
                                        </td>
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
                        
                        <!-- 1. SELECCIÓN DE ALUMNO (Jerarquía Visual) -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border-2 border-dashed border-gray-100">
                            <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> 1. Información del Estudiante
                            </h3>
                            <div class="max-w-xl">
                                @if(!$selectedAlumno)
                                    <div class="relative group">
                                        <input type="text" wire:model.live="search" placeholder="Escriba Nombre o DNI del alumno..." 
                                               class="w-full p-4 bg-white border-2 border-gray-50 rounded-2xl shadow-sm focus:border-indigo-500 transition-all outline-none">
                                        @if(count($resultados) > 0)
                                            <ul class="absolute z-50 mt-2 w-full bg-white shadow-2xl rounded-2xl border border-gray-100 py-3 max-h-64 overflow-y-auto custom-scrollbar">
                                                @foreach($resultados as $alumno)
                                                    <li wire:click="selectAlumno({{ $alumno->id }})" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer transition flex items-center justify-between group/item">
                                                        <div>
                                                            <div class="font-bold text-gray-800 group-hover/item:text-indigo-700">{{ $alumno->user->name }}</div>
                                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">DNI: {{ $alumno->dni }} • {{ $alumno->carrera->nombre ?? 'S/C' }}</div>
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
                                                {{ strtoupper(substr($selectedAlumno->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-lg leading-tight">{{ $selectedAlumno->user->name }}</div>
                                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-80">DNI: {{ $selectedAlumno->dni }} • Ciclo: {{ $selectedAlumno->ciclo->nombre ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('selectedAlumno', null)" class="p-2 hover:bg-white/10 rounded-xl transition group" title="Cambiar Alumno">
                                            <svg class="w-6 h-6 text-white/50 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endif
                                @error('selectedAlumno') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- 2. CONFIGURACIÓN (Heurística #4: Consistencia) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Monto Total Carrera</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">S/</span>
                                    <input type="number" step="0.01" wire:model="monto_total" class="w-full pl-10 p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700">
                                </div>
                                @error('monto_total') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Plan de Pagos</label>
                                <select wire:model.live="modalidad" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer">
                                    <option value="Pago Unico">Pago Único (1 Cuota)</option>
                                    <option value="2 Cuotas">Dividido en 2 Cuotas</option>
                                    <option value="3 Cuotas">Dividido en 3 Cuotas</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Estado Administrativo</label>
                                <select wire:model="estado" class="w-full p-3.5 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-gray-700 cursor-pointer">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Activa">Activa / Regular</option>
                                    <option value="Anulada">Anulada</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3. CRONOGRAMA (Heurística #8: Diseño Estético) -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Desglose de Pagos y Vouchers
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
                                        <div class="pt-2 border-t border-gray-50">
                                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-tighter block mb-2">Comprobante de Pago</label>
                                            @if(isset($cuota['existente_evidencia']) && $cuota['existente_evidencia'])
                                                <a href="{{ Storage::url($cuota['existente_evidencia']) }}" target="_blank" class="inline-flex items-center text-[10px] font-black text-indigo-600 hover:text-indigo-800 mb-3 bg-indigo-50 px-2 py-1 rounded-md transition-colors uppercase tracking-widest">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    Ver Voucher
                                                </a>
                                            @endif
                                            <input type="file" wire:model="cuotas.{{ $index }}.evidencia" class="block w-full text-[9px] text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. BOTONES (Heurística #3: Control y Libertad) -->
                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-10 border-t border-gray-100">
                            <button type="button" wire:click="closeModal" class="px-8 py-3.5 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">Cancelar</button>
                            <button type="submit" class="px-12 py-3.5 bg-indigo-600 text-white rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition active:scale-95 flex items-center justify-center">
                                <span wire:loading.remove wire:target="save">{{ $view == 'create' ? 'Confirmar Registro' : 'Actualizar Matrícula' }}</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

            @elseif($view == 'show' && $viewingMatricula)
                <!-- VISTA: DETALLES (SHOW) -->
                <div class="p-8 animate-fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Card Estudiante -->
                            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                                <h4 class="text-indigo-200 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Alumno Matriculado</h4>
                                <p class="text-3xl font-black leading-tight">{{ $viewingMatricula->alumno->user->name }}</p>
                                <p class="text-indigo-100 opacity-80 text-sm mt-2 font-medium">DNI: {{ $viewingMatricula->alumno->dni }}</p>
                                
                                <div class="mt-8 pt-8 border-t border-white/10">
                                    <p class="text-indigo-200 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Información Académica</p>
                                    <p class="text-sm font-bold">{{ $viewingMatricula->ciclo->nombre ?? 'Sin Ciclo' }}</p>
                                    <p class="text-[11px] text-indigo-100 italic mt-1">{{ $viewingMatricula->alumno->carrera->nombre ?? 'Sin Carrera' }}</p>
                                </div>
                            </div>
                            
                            <!-- Card Estado Resumen -->
                            <div class="bg-white rounded-3xl p-8 border-2 border-gray-50 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span> Resumen Financiero
                                </h4>
                                <div class="space-y-5">
                                    <div class="flex justify-between items-end border-b border-gray-50 pb-3">
                                        <span class="text-xs font-bold text-gray-400">Inversión Total:</span>
                                        <span class="text-lg font-black text-gray-800 font-mono">S/ {{ number_format($viewingMatricula->monto_total, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-end border-b border-gray-50 pb-3">
                                        <span class="text-xs font-bold text-gray-400">Modalidad:</span>
                                        <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-tighter">{{ $viewingMatricula->modalidad }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-400">Estado:</span>
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $viewingMatricula->estado == 'Activa' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $viewingMatricula->estado }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline de Pagos -->
                        <div class="lg:col-span-2 space-y-6">
                            <h3 class="text-xl font-black text-gray-800 tracking-tight flex items-center">
                                <svg class="w-6 h-6 mr-3 text-indigo-600 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Desglose de Pagos (Timeline)
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
                                            <p class="text-[11px] text-gray-400 font-bold flex items-center mt-1 uppercase tracking-tighter">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Vence: {{ \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            @if($pago->evidencia)
                                                <a href="{{ Storage::url($pago->evidencia) }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                                                    Ver Voucher
                                                </a>
                                            @endif
                                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border-2 {{ $pago->estado == 'Pagado' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-red-50 border-red-100 text-red-500' }}">
                                                {{ $pago->estado }}
                                            </span>
                                        </div>
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
</div>