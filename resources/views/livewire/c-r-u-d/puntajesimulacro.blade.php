<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS (Consistencia y Reconocimiento) -->
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
                        <span class="ml-1 text-sm font-bold text-indigo-600 md:ml-2 tracking-tight">Resultados de Simulacro</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ESTADO -->
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

        {{-- 3. VISTA PARA EL ALUMNO (Historial Personal) --}}
        @hasrole('alumno')
            <div class="bg-white shadow-sm border border-gray-100 sm:rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight italic">Mi Historial de Rendimiento</h2>
                        <p class="text-sm text-gray-500">Consulta tus puntajes y evolución en cada simulacro.</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Simulacro / Fecha</th>
                                <th class="px-6 py-4">Área Académica</th>
                                <th class="px-6 py-4 text-center">Correctas</th>
                                <th class="px-6 py-4 text-center">Incorrectas</th>
                                <th class="px-6 py-4 text-center">Blanco</th>
                                <th class="px-6 py-4 text-center">Puntaje Total</th>
                                <th class="px-6 py-4 text-center">Puesto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($misResultados as $res)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $res->simulacro->nombre }}</div>
                                        <div class="text-[10px] font-mono text-gray-400 mt-1 uppercase">{{ $res->simulacro->fecha->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-gray-200">
                                            {{ $res->simulacro->area->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-emerald-600 text-base">{{ $res->correctas }}</td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-rose-500 text-base">{{ $res->incorrectas }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-gray-400">{{ $res->blanco }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xl font-black text-indigo-700 font-mono tracking-tighter">{{ number_format($res->puntaje, 3) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center justify-center h-10 w-10 bg-amber-50 text-amber-600 rounded-xl border border-amber-100">
                                            <span class="text-[9px] font-black leading-none uppercase">Nº</span>
                                            <span class="text-base font-black leading-none">{{ $res->puesto }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">No se encontraron resultados registrados en tu historial.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endrole

        {{-- 4. VISTA PARA ADMIN / DOCENTE (Ingreso de Notas) --}}
        @hasanyrole('admin|docente')
            <div class="space-y-6">
                <!-- Filtros de Selección (Heurística #5: Prevención de errores) -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                    <div class="absolute right-6 top-6 h-10 w-10 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">1. Filtrar por Área</label>
                        <select wire:model.live="area_id" class="w-full bg-gray-50 border-2 border-gray-50 rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none p-3.5 cursor-pointer">
                            <option value="">Seleccione un Área Académica...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 {{ !$area_id ? 'text-gray-200' : '' }}">2. Seleccionar Simulacro</label>
                        <select wire:model.live="simulacro_id" 
                                class="w-full bg-gray-50 border-2 border-gray-50 rounded-xl font-bold text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none p-3.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" 
                                {{ !$area_id ? 'disabled' : '' }}>
                            <option value="">{{ !$area_id ? 'Debe elegir un área primero' : 'Seleccione el simulacro...' }}</option>
                            @foreach($simulacros as $sim)
                                <option value="{{ $sim->id }}">{{ $sim->nombre }} ({{ $sim->fecha->format('d/m/Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($simulacro_id && count($resultados) > 0)
                    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-3xl overflow-hidden animate-fade-in">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest">
                                    <tr>
                                        <th class="px-6 py-5">Información del Estudiante</th>
                                        <th class="px-4 py-5 text-center">Rpta. Correctas</th>
                                        <th class="px-4 py-5 text-center">Rpta. Incorrectas</th>
                                        <th class="px-4 py-5 text-center">En Blanco</th>
                                        <th class="px-6 py-5 text-center">Puntaje Calculado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($resultados as $id => $data)
                                        <tr class="hover:bg-gray-50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $data['nombre'] }}</div>
                                                <div class="text-[10px] font-mono text-gray-400 uppercase tracking-tighter">DNI: {{ $data['dni'] }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" wire:model="resultados.{{$id}}.correctas" wire:change="calcular({{$id}})" 
                                                       class="w-24 text-center bg-emerald-50 border-emerald-100 text-emerald-700 rounded-xl font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" wire:model="resultados.{{$id}}.incorrectas" wire:change="calcular({{$id}})" 
                                                       class="w-24 text-center bg-rose-50 border-rose-100 text-rose-700 rounded-xl font-bold focus:ring-rose-500 focus:border-rose-500 transition-all">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" wire:model="resultados.{{$id}}.blanco" 
                                                       class="w-24 text-center bg-gray-50 border-gray-100 text-gray-500 rounded-xl font-bold focus:ring-indigo-500 transition-all">
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="relative group">
                                                    <input type="number" step="0.001" wire:model="resultados.{{$id}}.puntaje" 
                                                           class="w-32 text-center bg-indigo-600 border-none text-white rounded-xl font-black text-base shadow-lg shadow-indigo-100 transition-all py-2" readonly>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón de Acción Principal (Heurística #1 y #3) -->
                        <div class="p-8 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest italic">Verifique los datos antes de procesar el ranking oficial.</p>
                            <button wire:click="save" 
                                    class="w-full md:w-auto px-12 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center">
                                <span wire:loading wire:target="save" class="mr-2 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                Procesar Notas y Generar Ranking
                            </button>
                        </div>
                    </div>
                @elseif($simulacro_id)
                    <div class="bg-white p-20 rounded-[3rem] border-2 border-dashed border-gray-100 text-center animate-fade-in">
                        <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold text-lg italic uppercase tracking-tighter">No hay alumnos inscritos en este ciclo para procesar resultados.</p>
                    </div>
                @endif
            </div>
        @endhasanyrole
    </div>

    <!-- Estilos de Animación (HCI: Feedback Visual) -->
    <style>
        .animate-fade-in-down { animation: fadeInDown 0.4s ease-out; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>