<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. BREADCRUMBS (Consistencia y Reconocimiento) -->
        <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                @foreach($breadcrumb as $item)
                    <li class="inline-flex items-center">
                        @if(!$loop->first)
                            <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        @endif
                        
                        @if($esAlumno && $item['step'] == 1)
                            <span class="text-sm font-medium text-gray-400 px-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                Inicio
                            </span>
                        @else
                            <button wire:click="goToStep({{ $item['step'] }})" 
                                class="text-sm font-semibold transition-colors flex items-center {{ $loop->last ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }}">
                                @if($loop->first)
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                @endif
                                {{ $item['name'] }}
                            </button>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- 2. PASO 1: SELECCIÓN DE CICLO (Dashboard de Selección) -->
        @if($step == 1 && !$esAlumno)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
                @foreach($ciclos as $ciclo)
                    <button wire:click="seleccionarCiclo({{ $ciclo->id }})"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all group text-left relative active:scale-95">
                        <div class="bg-indigo-600 p-5 group-hover:bg-indigo-700 transition-colors text-white">
                            <h3 class="font-black text-lg uppercase tracking-tight">{{ $ciclo->nombre }}</h3>
                        </div>
                        <div class="p-5 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Área Académica</p>
                                <p class="text-gray-700 font-bold text-sm">{{ $ciclo->area->nombre }}</p>
                            </div>
                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

        <!-- 3. PASO 2: HISTORIAL Y ACCIONES -->
        @elseif($step == 2)
            <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
                @if(!$esAlumno)
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-indigo-100 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1 bg-indigo-600"></div>
                        <h3 class="text-xl font-black text-gray-800 mb-6 uppercase tracking-tight flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Abrir Control de Asistencia
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Fecha de Registro</label>
                                <input type="date" wire:model="fecha" class="w-full rounded-xl border-2 border-gray-50 bg-gray-50 focus:bg-white focus:border-indigo-500 transition-all font-bold text-sm p-3 outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Turno Académico</label>
                                <select wire:model="turno" class="w-full rounded-xl border-2 border-gray-50 bg-gray-50 focus:bg-white focus:border-indigo-500 transition-all font-black text-xs uppercase p-3 outline-none cursor-pointer">
                                    <option value="mañana">🌅 Mañana</option>
                                    <option value="tarde">☀️ Tarde</option>
                                    <option value="noche">🌙 Noche</option>
                                </select>
                            </div>
                            <button wire:click="crearControl" class="bg-indigo-600 text-white p-3.5 rounded-xl font-black uppercase text-xs shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">
                                Iniciar Control
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 text-white relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 h-40 w-40 bg-white/10 rounded-full blur-3xl"></div>
                        <p class="text-[10px] font-black opacity-70 uppercase tracking-[0.2em] mb-2">Mi Ciclo Académico</p>
                        <h3 class="text-3xl font-black uppercase leading-tight">{{ $cicloSeleccionado['nombre'] }}</h3>
                        <p class="text-sm font-medium opacity-90 mt-1 italic">{{ $areaSeleccionada['nombre'] }}</p>
                    </div>
                @endif

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turno</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($controles as $ctrl)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 font-mono">{{ date('d/m/Y', strtotime($ctrl->fecha)) }}</td>
                                    <td class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-tighter">{{ $ctrl->turno }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight {{ $ctrl->estado == 'abierto' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $ctrl->estado == 'abierto' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                            {{ $ctrl->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="abrirControl({{ $ctrl->id }})" class="bg-white border-2 border-indigo-600 text-indigo-600 px-5 py-1.5 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                            {{ $esAlumno ? 'Marcar / Ver' : 'Gestionar Día' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- 4. PASO 3: PANEL DE MARCACIÓN (Grid de Estudiantes) -->
        @elseif($step == 3)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6 border-b border-gray-100 pb-8">
                    @if(!$esAlumno)
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" wire:model.live="search" placeholder="Filtrar por nombre o DNI..." class="w-full pl-10 p-3 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-medium text-sm">
                        </div>
                    @else
                        <div class="flex items-center text-indigo-600 font-black uppercase tracking-widest text-xs animate-pulse">
                            <span class="w-3 h-3 bg-indigo-600 rounded-full mr-2"></span>
                            Sistema de marcación en vivo
                        </div>
                    @endif
                    
                    <div class="text-center md:text-right">
                        <p class="text-xl font-black text-gray-800 uppercase tracking-tighter">{{ date('d/m/Y', strtotime($controlSeleccionado->fecha)) }}</p>
                        <div class="flex items-center justify-center md:justify-end gap-3 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Turno {{ $controlSeleccionado->turno }}</span>
                            @if($controlSeleccionado->estado == 'abierto' && !$esAlumno)
                                <button wire:click="cerrarControl" wire:confirm="¿Cerrar el control del día? Ya no se podrán realizar marcaciones." 
                                        class="bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1 rounded-lg text-[9px] font-black uppercase hover:bg-rose-600 hover:text-white transition-all">
                                    Cerrar Día
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Grid de Alumnos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($alumnos as $alumno)
                        @php
                            $asistencia = $alumno->asistencias->first();
                            $estado = $asistencia ? $asistencia->estado : 'falta';
                            
                            $colorCard = [
                                'presente' => 'border-emerald-500 bg-emerald-50/50',
                                'tardanza' => 'border-amber-500 bg-amber-50/50',
                                'falta' => 'border-gray-100 bg-white shadow-sm'
                            ][$estado] ?? 'border-gray-100 bg-white';
                        @endphp
                        
                        <div class="relative p-6 rounded-3xl border-2 transition-all duration-300 {{ $colorCard }} group overflow-hidden">
                            <!-- Indicador de Estado Superior -->
                            <div class="absolute top-0 right-0 p-3">
                                <span class="h-2 w-2 rounded-full block {{ $estado == 'presente' ? 'bg-emerald-500' : ($estado == 'tardanza' ? 'bg-amber-500' : 'bg-gray-300') }}"></span>
                            </div>

                            <h4 class="font-black text-gray-800 uppercase text-xs truncate leading-tight pr-4">{{ $alumno->user->name }}</h4>
                            <p class="text-[10px] text-gray-400 font-mono mt-1">DNI: {{ $alumno->dni }}</p>

                            <div class="mt-6">
                                @if(!$esAlumno)
                                    <div class="flex gap-2">
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'presente')" 
                                                class="flex-1 py-2 rounded-xl text-xs font-black transition-all {{ $estado == 'presente' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100 scale-105' : 'bg-white border border-gray-100 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600' }}">P</button>
                                        
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'tardanza')" 
                                                class="flex-1 py-2 rounded-xl text-xs font-black transition-all {{ $estado == 'tardanza' ? 'bg-amber-500 text-white shadow-lg shadow-amber-100 scale-105' : 'bg-white border border-gray-100 text-gray-400 hover:bg-amber-50 hover:text-amber-600' }}">T</button>
                                        
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'falta')" 
                                                class="flex-1 py-2 rounded-xl text-xs font-black transition-all {{ $estado == 'falta' ? 'bg-rose-600 text-white shadow-lg shadow-rose-100 scale-105' : 'bg-white border border-gray-100 text-gray-400 hover:bg-rose-50 hover:text-rose-600' }}">F</button>
                                    </div>
                                @else
                                    @if($controlSeleccionado->estado == 'abierto' && $estado != 'presente')
                                        <button wire:click="marcarAsistencia({{ Auth::id() }}, 'presente')" 
                                                class="w-full py-3 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">
                                            Marcar Asistencia
                                        </button>
                                    @else
                                        <div class="w-full py-2.5 text-center rounded-xl font-black uppercase text-[10px] tracking-widest {{ $estado == 'presente' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $estado == 'presente' ? '¡Presente Registrado!' : 'Fuera de Tiempo' }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Estilos Adicionales para Animaciones (HCI: Feedback Visual) -->
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div>