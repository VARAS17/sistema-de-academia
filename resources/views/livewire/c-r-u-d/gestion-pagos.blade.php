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
                        <button wire:click="showIndex" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">
                            {{ auth()->user()->hasRole('alumno') ? 'Mis Pagos' : 'Gestión de Pagos' }}
                        </button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'detalle' ? 'Detalles de Pago' : 'Verificación de Voucher' }}
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex justify-between items-center animate-fade-in-down">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all">

            <!-- VISTA: LISTADO (INDEX) -->
            @if($view == 'index')
                <div class="p-6">
                    @hasanyrole('admin|docente')
                        <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="relative w-full md:w-1/3 group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input wire:model.live.debounce.300ms="search" type="text" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 focus:bg-white transition-all outline-none" 
                                       placeholder="Buscar por alumno o DNI...">
                            </div>
                            <div class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                Pagos registrados: <span class="text-indigo-600">{{ $pagos->total() }}</span>
                            </div>
                        </div>
                    @endhasanyrole

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Concepto / Ciclo</th>
                                    @hasanyrole('admin|docente') <th class="px-6 py-4">Estudiante</th> @endhasanyrole
                                    <th class="px-6 py-4 text-center">Vencimiento</th>
                                    <th class="px-6 py-4 text-center">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($pagos as $pago)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $pago->concepto }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $pago->matricula->ciclo->nombre }}</div>
                                        </td>
                                        @hasanyrole('admin|docente')
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-[10px] mr-3">
                                                        {{ strtoupper(substr($pago->matricula->alumno->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-xs font-bold text-gray-700 leading-tight">{{ $pago->matricula->alumno->user->name }}</div>
                                                        <div class="text-[9px] text-gray-400 font-mono">DNI: {{ $pago->matricula->alumno->dni }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endhasanyrole
                                        <td class="px-6 py-4 text-center font-mono text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight
                                                {{ $pago->estado == 'Pagado' ? 'bg-green-100 text-green-700' : 
                                                   ($pago->estado == 'Observado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                <span class="w-1 h-1 rounded-full mr-1.5 {{ $pago->estado == 'Pagado' ? 'bg-green-500' : ($pago->estado == 'Observado' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                                                {{ $pago->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-2">
                                                <button wire:click="verDetalle({{ $pago->id }})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors relative inline-flex items-center justify-center flex-shrink-0" title="Ver Detalle">
                                                    <img src="{{ asset('metaforas/PAGOS.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                    <svg class="w-4 h-4 absolute bottom-1.5 right-1.5 bg-white text-black rounded-full shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                
                                                @hasanyrole('admin|docente')
                                                    @if($pago->estado !== 'Pagado')
                                                        <button wire:click="registrarPago({{ $pago->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Subir / Verificar Voucher">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </button>
                                                    @endif
                                                @endhasanyrole

                                                <button wire:click="exportarPDF({{ $pago->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Descargar Boleta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No se encontraron registros de pagos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $pagos->links() }}</div>
                </div>

            <!-- VISTA: DETALLE DE PAGO (SHOW) -->
            @elseif($view == 'detalle')
                <div class="p-8 max-w-5xl mx-auto animate-fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100">
                                <h4 class="text-indigo-200 text-[10px] font-black uppercase tracking-widest mb-4">Información Estudiante</h4>
                                <p class="text-2xl font-black leading-tight">{{ $pagoSeleccionado->matricula->alumno->user->name }}</p>
                                <p class="text-indigo-100 text-xs font-mono mt-1 italic">DNI: {{ $pagoSeleccionado->matricula->alumno->dni }}</p>
                                
                                <div class="mt-8 pt-8 border-t border-white/10 space-y-4">
                                    <div>
                                        <p class="text-indigo-200 text-[9px] font-black uppercase tracking-widest">Concepto</p>
                                        <p class="text-sm font-bold">{{ $pagoSeleccionado->concepto }}</p>
                                    </div>
                                    <div>
                                        <p class="text-indigo-200 text-[9px] font-black uppercase tracking-widest">Importe</p>
                                        <p class="text-xl font-black">S/ {{ number_format($pagoSeleccionado->monto, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Ciclo:</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $pagoSeleccionado->matricula->ciclo->nombre }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">F. Pago:</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $pagoSeleccionado->fecha_pago ? \Carbon\Carbon::parse($pagoSeleccionado->fecha_pago)->format('d/m/Y') : 'Pendiente' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white p-4 rounded-[2rem] border-2 border-gray-50 shadow-inner h-full flex flex-col">
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 px-4 pt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Evidencia del Voucher
                                </h3>
                                <div class="flex-grow flex items-center justify-center bg-gray-50 rounded-2xl overflow-hidden min-h-[400px] border border-dashed border-gray-200">
                                    @if($pagoSeleccionado->evidencia)
                                        <img src="{{ asset('storage/' . $pagoSeleccionado->evidencia) }}" 
                                             class="max-h-[500px] object-contain hover:scale-105 transition-transform cursor-zoom-in" 
                                             onclick="window.open(this.src)">
                                    @else
                                        <div class="flex flex-col items-center text-gray-400">
                                            <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-xs font-bold uppercase tracking-widest">Sin voucher adjunto</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-10 flex justify-end">
                        <button wire:click="showIndex" class="px-10 py-3 bg-white border-2 border-gray-100 text-gray-400 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-all">Regresar</button>
                    </div>
                </div>

            <!-- VISTA: SUBIR VOUCHER / VERIFICACIÓN (CREATE/EDIT) -->
            @elseif($view == 'subir_voucher')
                <div class="p-8 max-w-2xl mx-auto animate-fade-in">
                    <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-6">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Verificación Académica</h2>
                            <p class="text-sm text-gray-500 italic">Validar comprobante de pago enviado por el alumno.</p>
                        </div>
                        <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Fecha de Operación</label>
                                <input type="date" wire:model="fecha_pago" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-sm">
                                @error('fecha_pago') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Estado de la Verificación</label>
                                <select wire:model="estado" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-sm cursor-pointer appearance-none">
                                    <option value="Pagado">Pagado (Validado)</option>
                                    <option value="Observado">Observado (Rechazar)</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-6 bg-indigo-50/50 rounded-2xl border-2 border-dashed border-indigo-100">
                            <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3 ml-1">Actualizar Voucher (Imagen)</label>
                            <input type="file" wire:model="evidencia" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                            @error('evidencia') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tight">{{ $message }}</p> @enderror
                            
                            <div wire:loading wire:target="evidencia" class="mt-2 text-[10px] text-indigo-500 font-bold animate-pulse italic">Cargando archivo...</div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-10 pt-6 border-t border-gray-100">
                            <button type="button" wire:click="showIndex" class="px-8 py-3 text-xs font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                                Cancelar
                            </button>
                            <button type="submit" class="px-10 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                                <span wire:loading wire:target="save" class="mr-2 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                Confirmar Verificación
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>