<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. SISTEMA DE BREADCRUMBS -->
        <nav class="flex mb-6 text-sm flex-wrap items-center text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001.111 1H7v-6h6v6h1.889a1 1 0 001.111-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('CRUD.asistencias') }}" class="ml-1 md:ml-2 hover:text-indigo-600 transition-colors">Asistencia</a>
                    </div>
                </li>
                @foreach($breadcrumb as $item)
                    @if(!Str::contains(Str::lower($item['name']), ['inicio', 'asistencia']))
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                @if($loop->last)
                                    <span class="ml-1 md:ml-2 text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-xs uppercase tracking-tight border border-indigo-100">{{ $item['name'] }}</span>
                                @else
                                    <button wire:click="goToStep({{ $item['step'] }})" class="ml-1 md:ml-2 hover:text-indigo-600 transition-colors">{{ $item['name'] }}</button>
                                @endif
                            </div>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <!-- 2. PASO 1: SELECCIÓN DE CICLO -->
        @if($step == 1 && !$esAlumno)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
                @foreach($ciclos as $ciclo)
                    <button wire:click="seleccionarCiclo({{ $ciclo->id }})" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all group text-left relative active:scale-95">
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

        <!-- 3. PASO 2: HISTORIAL -->
        @elseif($step == 2)
            <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 text-white relative overflow-hidden flex justify-between items-center">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black opacity-70 uppercase tracking-[0.2em] mb-2">{{ $esAlumno ? 'Mi Ciclo Académico' : 'Panel de Gestión' }}</p>
                        <h3 class="text-3xl font-black uppercase leading-tight">{{ $cicloSeleccionado['nombre'] }}</h3>
                        <p class="text-sm font-medium opacity-90 mt-1 italic">{{ $areaSeleccionada['nombre'] }}</p>
                    </div>
                    @if(!$esAlumno)
                        <button wire:click="mostrarFormularioCreacion" class="relative z-10 bg-white text-indigo-600 px-6 py-3 rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-indigo-50 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Nueva Sesión
                        </button>
                    @endif
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turno</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($controles as $ctrl)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 font-mono">{{ date('d/m/Y', strtotime($ctrl->fecha)) }}</td>
                                    <td class="px-6 py-4 text-xs font-black uppercase text-gray-500">{{ $ctrl->turno }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $ctrl->estado == 'abierto' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $ctrl->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="abrirControl({{ $ctrl->id }})" class="bg-white border-2 border-indigo-600 text-indigo-600 px-5 py-1.5 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 hover:text-white transition-all">
                                            {{ $esAlumno ? 'Ver' : 'Gestionar' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- 4. PASO 4: NUEVA SESIÓN -->
        @elseif($step == 4)
            <div class="max-w-xl mx-auto animate-fade-in">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-100 border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-600 p-8 text-center"><h3 class="text-2xl font-black text-white uppercase">Configurar Sesión</h3></div>
                    <div class="p-10 space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Fecha</label>
                            <input type="date" wire:model="fecha" class="w-full rounded-2xl border-2 border-gray-50 bg-gray-50 p-4 font-bold outline-none focus:border-indigo-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Turno</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['mañana' => '🌅', 'tarde' => '☀️', 'noche' => '🌙'] as $val => $icon)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" wire:model="turno" value="{{ $val }}" class="peer sr-only">
                                        <div class="p-4 text-center rounded-2xl border-2 border-gray-50 bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition-all">
                                            <span class="block text-xl">{{ $icon }}</span>
                                            <span class="block text-[10px] font-black uppercase">{{ $val }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button wire:click="goToStep(2)" class="flex-1 font-black uppercase text-xs text-gray-400">Cancelar</button>
                            <button wire:click="guardarControl" class="flex-[2] bg-indigo-600 text-white p-4 rounded-2xl font-black uppercase text-xs shadow-xl">Crear Sesión</button>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 5. PASO 3: PANEL DE MARCACIÓN -->
        @elseif($step == 3)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6 border-b border-gray-100 pb-8">
                    @if(!$esAlumno)
                        <div class="relative w-full md:w-1/3 group">
                            <input type="text" wire:model.live="search" placeholder="Filtrar por nombre..." class="w-full pl-10 p-3 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:border-indigo-500 outline-none">
                        </div>
                    @endif
                    <div class="text-center md:text-right">
                        <p class="text-xl font-black text-gray-800 uppercase">{{ date('d/m/Y', strtotime($controlSeleccionado->fecha)) }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Turno {{ $controlSeleccionado->turno }}</span>
                            @if($controlSeleccionado->estado == 'abierto' && !$esAlumno)
                                <!-- BOTÓN QUE ABRE EL MODAL -->
                                <button wire:click="abrirConfirmacionCierre" 
                                        class="bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1 rounded-lg text-[9px] font-black uppercase hover:bg-rose-600 hover:text-white transition-all">
                                    Cerrar Día
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($alumnos as $alumno)
                        @php
                            $asistencia = $alumno->asistencias->first();
                            $estado = $asistencia ? $asistencia->estado : 'falta';
                            $colorCard = ['presente' => 'border-emerald-500 bg-emerald-50/50', 'tardanza' => 'border-amber-500 bg-amber-50/50', 'falta' => 'border-gray-100 bg-white shadow-sm'][$estado] ?? 'border-gray-100 bg-white';
                        @endphp
                        <div class="relative p-6 rounded-3xl border-2 transition-all {{ $colorCard }} overflow-hidden">
                            <h4 class="font-black text-gray-800 uppercase text-xs truncate">{{ $alumno->user->name }}</h4>
                            <div class="mt-6">
                                @if(!$esAlumno)
                                    <div class="flex gap-2">
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'presente')" class="flex-1 py-2 rounded-xl text-xs font-black {{ $estado == 'presente' ? 'bg-emerald-600 text-white' : 'bg-white border text-gray-400' }}">P</button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'tardanza')" class="flex-1 py-2 rounded-xl text-xs font-black {{ $estado == 'tardanza' ? 'bg-amber-500 text-white' : 'bg-white border text-gray-400' }}">T</button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'falta')" class="flex-1 py-2 rounded-xl text-xs font-black {{ $estado == 'falta' ? 'bg-rose-600 text-white' : 'bg-white border text-gray-400' }}">F</button>
                                    </div>
                                @else
                                    <button wire:click="marcarAsistencia({{ Auth::id() }}, 'presente')" class="w-full py-3 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[10px]">Marcar Asistencia</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 6. MODAL DE CONFIRMACIÓN (ESTILO PERSONALIZADO) -->
        @if($confirmandoCierre)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-[2.5rem] max-w-md w-full shadow-2xl overflow-hidden border border-gray-100">
                    <div class="p-8">
                        <div class="flex items-start">
                            <!-- Icono de Advertencia -->
                            <div class="flex-shrink-0 bg-rose-100 p-3 rounded-2xl text-rose-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            
                            <!-- Contenido solicitado -->
                            <div class="ml-6">
                                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Confirmar Cierre de Día?</h3>
                                <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">
                                    Esta acción finalizará la toma de asistencia para esta sesión de forma permanente. Una vez cerrado, el sistema no permitirá realizar más marcaciones o modificaciones para proteger la integridad del registro.
                                </p>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-10 flex gap-3">
                            <button wire:click="cerrarConfirmacionCierre" 
                                    class="flex-1 px-6 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase text-[10px] hover:bg-gray-100 transition-all">
                                Cancelar
                            </button>
                            <button wire:click="cerrarControl" 
                                    class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl shadow-rose-200 hover:bg-rose-700 active:scale-95 transition-all">
                                Sí, Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>