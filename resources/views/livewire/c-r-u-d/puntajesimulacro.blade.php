<div class="py-8 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS -->
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
                        <span class="ml-1 text-sm font-bold text-indigo-600 md:ml-2 tracking-tight">Gestión de Puntajes</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ESTADO GLOBAL -->
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

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 shadow-sm rounded-r-xl flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- 3. VISTA PARA ADMIN / DOCENTE --}}
        @hasanyrole('admin|docente')
            <div class="space-y-6">
                
                <!-- FILTROS EN CASCADA -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">1. Área Académica</label>
                        <select wire:model.live="area_id" class="w-full bg-gray-50 border-2 border-transparent rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all p-3.5 outline-none">
                            <option value="">Seleccione Área...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">2. Ciclo Académico</label>
                        <select wire:model.live="ciclo_id" class="w-full bg-gray-50 border-2 border-transparent rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all p-3.5 outline-none disabled:opacity-40" {{ !$area_id ? 'disabled' : '' }}>
                            <option value="">Seleccione Ciclo...</option>
                            @foreach($ciclos as $ciclo)
                                <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">3. Simulacro</label>
                        <select wire:model.live="simulacro_id" class="w-full bg-gray-50 border-2 border-transparent rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all p-3.5 outline-none disabled:opacity-40" {{ !$ciclo_id ? 'disabled' : '' }}>
                            <option value="">Seleccione Simulacro...</option>
                            @foreach($simulacros as $sim)
                                <option value="{{ $sim->id }}">{{ $sim->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- TABLA DE RESULTADOS -->
                @if($simulacro_id && count($resultados) > 0)
                    <div class="bg-white shadow-xl border border-gray-100 sm:rounded-3xl overflow-hidden animate-fade-in">
                        
                        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest italic ml-2">Registro de Notas (Máx. 100 Preguntas)</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest">
                                    <tr>
                                        <th class="px-6 py-5">Estudiante</th>
                                        <th class="px-4 py-5 text-center w-32">Correctas</th>
                                        <th class="px-4 py-5 text-center w-32">Incorrectas</th>
                                        <th class="px-4 py-5 text-center w-32">Blanco</th>
                                        <th class="px-4 py-5 text-center w-24">Suma</th>
                                        <th class="px-6 py-5 text-center w-40">Puntaje Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($resultados as $id => $data)
                                        @php
                                            $totalFila = (int)$data['correctas'] + (int)$data['incorrectas'] + (int)$data['blanco'];
                                            $isError = $data['error_suma'] || $totalFila > 100;
                                        @endphp
                                        <tr class="hover:bg-indigo-50/30 transition-colors {{ $isError ? 'bg-red-50/50' : '' }}">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 uppercase">{{ $data['nombre'] }}</div>
                                                <div class="text-[10px] font-mono text-gray-400">DNI: {{ $data['dni'] }}</div>
                                                @if(session()->has("error_row_$id"))
                                                    <span class="text-[10px] text-red-600 font-bold animate-pulse">{{ session("error_row_$id") }}</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Inputs con restricción de enteros en el teclado (onkeypress) -->
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                       wire:model.blur="resultados.{{$id}}.correctas" wire:change="calcular({{$id}})" 
                                                       class="w-full text-center bg-white border-2 {{ $isError ? 'border-red-300' : 'border-emerald-100' }} text-emerald-700 rounded-xl font-bold focus:ring-emerald-500 transition-all">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                       wire:model.blur="resultados.{{$id}}.incorrectas" wire:change="calcular({{$id}})" 
                                                       class="w-full text-center bg-white border-2 {{ $isError ? 'border-red-300' : 'border-rose-100' }} text-rose-700 rounded-xl font-bold focus:ring-rose-500 transition-all">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                       wire:model.blur="resultados.{{$id}}.blanco" wire:change="calcular({{$id}})"
                                                       class="w-full text-center bg-white border-2 {{ $isError ? 'border-red-300' : 'border-gray-100' }} text-gray-500 rounded-xl font-bold focus:ring-indigo-500 transition-all">
                                            </td>

                                            <!-- Visualizador de suma total de la fila -->
                                            <td class="px-4 py-4 text-center">
                                                <span class="text-xs font-black {{ $isError ? 'text-red-600' : 'text-gray-400' }}">
                                                    {{ $totalFila }}/100
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-block w-full py-2 {{ $isError ? 'bg-red-400' : 'bg-indigo-600' }} text-white rounded-xl font-black text-base shadow-sm">
                                                    {{ number_format($data['puntaje'], 3) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón de Guardado -->
                        <div class="p-8 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-[10px] font-bold uppercase tracking-widest italic">Solo se permiten números enteros. La suma debe ser ≤ 100.</p>
                            </div>
                            <button wire:click="save" 
                                    class="w-full md:w-auto px-12 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center">
                                <span wire:loading wire:target="save" class="mr-2 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                Guardar y Recalcular Ranking
                            </button>
                        </div>
                    </div>
                @elseif($simulacro_id)
                    <div class="bg-white p-20 rounded-[3rem] border-2 border-dashed border-gray-100 text-center animate-fade-in">
                        <p class="text-gray-400 font-bold text-lg italic uppercase">No hay alumnos con matrícula activa.</p>
                    </div>
                @else
                    <div class="bg-indigo-50/50 p-20 rounded-[3rem] border-2 border-dashed border-indigo-100 text-center">
                        <p class="text-indigo-400 font-black text-lg uppercase tracking-widest">Seleccione los filtros para cargar la lista</p>
                    </div>
                @endif
            </div>
        @endhasanyrole

        {{-- 4. VISTA PARA EL ALUMNO --}}
        @hasrole('alumno')
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-black text-gray-800 uppercase">Mi Historial de Simulacros</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-900 text-white text-[10px] font-black uppercase">
                            <tr>
                                <th class="px-6 py-4">Simulacro / Fecha</th>
                                <th class="px-4 py-4 text-center">Correctas</th>
                                <th class="px-4 py-4 text-center">Incorrectas</th>
                                <th class="px-6 py-4 text-center">Puntaje</th>
                                <th class="px-6 py-4 text-center">Puesto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($misResultados as $res)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $res->simulacro->nombre }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $res->simulacro->fecha->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-emerald-600">{{ $res->correctas }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-rose-600">{{ $res->incorrectas }}</td>
                                    <td class="px-6 py-4 text-center font-black text-indigo-600 text-lg">{{ number_format($res->puntaje, 3) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full font-black text-xs">
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
</div>