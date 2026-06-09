<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
        
        @if($data['role'] === 'sin_perfil')
            {{-- CASO: Usuario sin perfil de alumno (probablemente un usuario recién creado) --}}
            <div class="flex flex-col items-center justify-center h-64 bg-white dark:bg-neutral-800 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700">
                <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h2 class="text-xl font-bold text-neutral-800 dark:text-neutral-200 text-center uppercase tracking-wider">Perfil no encontrado</h2>
                <p class="text-neutral-500 dark:text-neutral-400 text-center max-w-md px-4 mt-2">
                    Tu usuario no tiene un perfil de alumno asociado. Si eres administrador, usa el panel lateral; si eres alumno, contacta a secretaría para tu registro.
                </p>
            </div>
        @else

            {{-- --- FILA DE WIDGETS SUPERIORES (Estadísticas Rápidas) --- --}}
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                
                @if($data['role'] === 'admin')
                    <!-- ADMIN: Total Alumnos -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Total Alumnos</p>
                                <p class="mt-2 text-4xl font-black text-neutral-900 dark:text-neutral-100">{{ $data['total_alumnos'] }}</p>
                            </div>
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- ADMIN: Pagos Pendientes -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm border-l-4 border-l-amber-500">
                        <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Pagos Pendientes</p>
                        <p class="mt-2 text-4xl font-black text-amber-500">{{ $data['pagos_pendientes'] }}</p>
                        <p class="text-[10px] text-neutral-400 mt-2 italic font-medium">Por confirmar en sistema</p>
                    </div>

                    <!-- ADMIN: Recaudación -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
                        <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Recaudación Total</p>
                        <p class="mt-2 text-3xl font-black text-emerald-600">S/ {{ number_format($data['monto_recaudado'], 2) }}</p>
                        <p class="text-[10px] text-neutral-400 mt-2 italic font-medium">Ingresos confirmados</p>
                    </div>
                @else
                    <!-- ALUMNO: Carrera y Asistencia -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-indigo-600 shadow-lg text-white">
                        <p class="text-xs font-bold opacity-70 uppercase tracking-widest">Mi Carrera</p>
                        <p class="mt-1 text-xl font-black truncate leading-tight">{{ $data['alumno']->carrera->nombre ?? 'Sin carrera' }}</p>
                        <div class="mt-4">
                            <div class="flex justify-between text-[10px] mb-1 font-bold uppercase">
                                <span>Asistencia General</span>
                                <span>{{ $data['porcentaje_asistencia'] }}%</span>
                            </div>
                            <div class="w-full bg-indigo-900/40 rounded-full h-1.5">
                                <div class="bg-white h-1.5 rounded-full shadow-[0_0_8px_rgba(255,255,255,0.5)]" style="width: {{ $data['porcentaje_asistencia'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ALUMNO: Próximo Pago -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
                        <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Próxima Cuota</p>
                        @if($data['proximo_pago'])
                            <p class="mt-2 text-3xl font-black text-rose-600 uppercase italic">S/ {{ number_format($data['proximo_pago']->monto, 2) }}</p>
                            <p class="mt-1 text-[10px] font-bold text-neutral-500 uppercase">Vence: {{ \Carbon\Carbon::parse($data['proximo_pago']->fecha_vencimiento)->format('d/m/Y') }}</p>
                        @else
                            <p class="mt-2 text-xl font-black text-emerald-500 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" /></svg>
                                ESTÁS AL DÍA
                            </p>
                        @endif
                    </div>

                    <!-- ALUMNO: Último Simulacro -->
                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm border-r-4 border-r-indigo-500">
                        <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Último Puntaje</p>
                        @if($data['ultimo_resultado'])
                            <p class="mt-2 text-4xl font-black text-neutral-900 dark:text-neutral-100">{{ $data['ultimo_resultado']->puntaje }}</p>
                            <p class="mt-1 text-xs font-black text-indigo-500 uppercase tracking-tighter">Puesto: #{{ $data['ultimo_resultado']->puesto }}</p>
                        @else
                            <p class="mt-2 text-lg font-medium text-neutral-400 italic">Sin exámenes</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- --- ÁREA PRINCIPAL (Tablas de Datos) --- --}}
            <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm overflow-y-auto">
                
                @if($data['role'] === 'admin')
                    {{-- TABLA ADMIN: RENDIMIENTO POR ÁREA --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-black text-neutral-800 dark:text-neutral-200 uppercase tracking-tighter">Rendimiento por Área Académica</h3>
                            <p class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest">Promedios generales basados en simulacros</p>
                        </div>
                        <div class="h-1 w-16 bg-indigo-500 rounded-full"></div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-neutral-400 border-b border-neutral-100 dark:border-neutral-700">
                                    <th class="pb-3 font-bold uppercase text-[10px]">Nombre del Área</th>
                                    <th class="pb-3 font-bold uppercase text-[10px] text-center">N° Alumnos</th>
                                    <th class="pb-3 font-bold uppercase text-[10px] text-center">Puntaje Máx.</th>
                                    <th class="pb-3 font-bold uppercase text-[10px] text-right">Promedio General</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50 dark:divide-neutral-700/50">
                                @foreach($data['rendimiento_areas'] as $area)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">
                                    <td class="py-4">
                                        <div class="font-bold text-neutral-800 dark:text-neutral-100">{{ $area->nombre }}</div>
                                        <div class="text-[9px] text-indigo-500 font-bold uppercase">Sector Académico</div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="bg-neutral-100 dark:bg-neutral-700 px-2.5 py-1 rounded-md font-bold text-xs">
                                            {{ $area->total_alumnos }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center font-bold text-emerald-600">
                                        {{ number_format($area->puntaje_maximo, 2) ?? '0.00' }}
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="font-black text-indigo-500 text-lg">
                                            {{ number_format($area->promedio_area, 2) ?? '0.00' }}
                                        </div>
                                        <div class="w-24 ml-auto bg-neutral-100 dark:bg-neutral-700 h-1 rounded-full mt-1">
                                            <div class="bg-indigo-500 h-1 rounded-full" style="width: {{ min(($area->promedio_area / 20) * 100, 100) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- VISTA ALUMNO: Historial Personal y Pagos --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        
                        {{-- Columna 1: Mis Simulacros --}}
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
                                <h3 class="text-md font-black text-neutral-800 dark:text-neutral-200 uppercase tracking-tighter">Historial de Simulacros</h3>
                            </div>
                            <div class="space-y-3">
                                @forelse($data['mis_resultados'] as $r)
                                    <div class="group flex items-center justify-between p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/30">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-neutral-800 dark:text-neutral-200">{{ $r->simulacro->nombre ?? 'Simulacro General' }}</span>
                                            <span class="text-[10px] text-neutral-400 font-bold uppercase italic">C: {{ $r->correctas }} | I: {{ $r->incorrectas }}</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ $r->puntaje }}</div>
                                            <div class="text-[10px] font-bold text-neutral-400 uppercase">Puesto {{ $r->puesto }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center border-2 border-dashed border-neutral-100 dark:border-neutral-800 rounded-2xl">
                                        <p class="text-sm text-neutral-400 italic">No hay exámenes registrados.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Columna 2: Mis Pagos --}}
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                                <h3 class="text-md font-black text-neutral-800 dark:text-neutral-200 uppercase tracking-tighter">Mis Pagos</h3>
                            </div>
                            <div class="rounded-xl border border-neutral-100 dark:border-neutral-700 overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                                        <tr class="text-left text-neutral-400 uppercase text-[9px] font-black tracking-widest">
                                            <th class="px-4 py-3">Concepto</th>
                                            <th class="px-4 py-3">Monto</th>
                                            <th class="px-4 py-3 text-right">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-50 dark:divide-neutral-700/50">
                                        @foreach($data['mis_pagos'] as $pago)
                                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/20 transition-colors">
                                                <td class="px-4 py-4 text-neutral-600 dark:text-neutral-400 font-bold">Cuota #{{ $pago->numero_cuota }}</td>
                                                <td class="px-4 py-4 font-black dark:text-neutral-200 text-md">S/ {{ number_format($pago->monto, 2) }}</td>
                                                <td class="px-4 py-4 text-right">
                                                    <span class="inline-block px-3 py-1 rounded-full text-[9px] font-black {{ $pago->estado === 'pagado' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                                        {{ strtoupper($pago->estado) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif
    </div>
</x-layouts::app>