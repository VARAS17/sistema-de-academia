<div class="py-8 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 1. SISTEMA DE BREADCRUMBS CORREGIDO -->
        <nav class="flex mb-6 text-sm items-center text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <!-- El Inicio siempre redirige al Dashboard principal -->
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-indigo-600 transition-colors font-medium">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001.111 1H7v-6h6v6h1.889a1 1 0 001.111-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Inicio
                    </a>
                </li>

                @foreach($breadcrumb as $item)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            @if($loop->last)
                                <span class="ml-1 md:ml-2 text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-lg text-[10px] uppercase border border-indigo-100 tracking-tight">
                                    {{ $item['name'] }}
                                </span>
                            @else
                                <button wire:click="goToStep({{ $item['step'] }})" class="ml-1 md:ml-2 hover:text-indigo-600 transition-colors font-medium uppercase text-[10px]">
                                    {{ $item['name'] }}
                                </button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- 2. PASO 1: SELECCIÓN DE CICLO (Solo Admin) -->
        @if($step == 1 && !$esAlumno)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
                @foreach($ciclos as $ciclo)
                    <button wire:click="seleccionarCiclo({{ $ciclo->id }})" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-indigo-300 transition-all group text-left active:scale-95">
                        <div class="bg-indigo-600 p-5 text-white group-hover:bg-indigo-700 transition-colors">
                            <h3 class="font-black text-lg uppercase tracking-tight">{{ $ciclo->nombre }}</h3>
                        </div>
                        <div class="p-5 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Área Académica</p>
                                <p class="text-gray-700 font-bold text-sm">{{ $ciclo->area->nombre }}</p>
                            </div>
                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

        <!-- 3. PASO 2: LISTA DE SESIONES / HISTORIAL -->
        @elseif($step == 2)
            <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 text-white flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-[10px] font-black opacity-70 uppercase tracking-[0.2em] mb-1">Registro de Asistencias</p>
                        <h3 class="text-2xl font-black uppercase tracking-tight">{{ $cicloSeleccionado['nombre'] }}</h3>
                        <p class="text-xs font-medium opacity-80 italic">{{ $areaSeleccionada['nombre'] ?? '' }}</p>
                    </div>
                    @if(!$esAlumno)
                        <button wire:click="mostrarFormularioCreacion" class="bg-white text-indigo-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] shadow-lg hover:bg-indigo-50 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
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
                                    <td class="px-6 py-4 text-[10px] font-black uppercase text-gray-400">{{ $ctrl->turno }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $ctrl->estado == 'abierto' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $ctrl->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="abrirControl({{ $ctrl->id }})" class="bg-white border border-indigo-600 text-indigo-600 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase hover:bg-indigo-600 hover:text-white transition-all">
                                            {{ $esAlumno ? 'Ver Detalle' : 'Gestionar' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- 4. PASO 3: PANEL DE MARCACIÓN (INTERFAZ MEJORADA ESTILO LISTA) -->
        @elseif($step == 3)
            <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6 border-b border-gray-50 pb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">{{ date('d/m/Y', strtotime($controlSeleccionado->fecha)) }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sesión: {{ $controlSeleccionado->turno }}</span>
                            <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                            <span class="text-[10px] font-black {{ $controlSeleccionado->estado == 'abierto' ? 'text-emerald-500' : 'text-rose-500' }} uppercase italic">{{ $controlSeleccionado->estado }}</span>
                        </div>
                    </div>

                    @if(!$esAlumno && $controlSeleccionado->estado == 'abierto')
                        <button wire:click="abrirConfirmacionCierre" class="bg-rose-50 text-rose-600 border border-rose-100 px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-rose-600 hover:text-white transition-all">
                            Finalizar Día
                        </button>
                    @endif
                </div>

                @if(!$esAlumno)
                    <div class="relative mb-6">
                        <input type="text" wire:model.live="search" placeholder="Buscar por Nombre o DNI..." class="w-full pl-10 p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                        <svg class="w-5 h-5 absolute left-3 top-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($alumnos as $alumno)
                        @php
                            $asistencia = $alumno->asistencias->first();
                            $estadoActual = $asistencia ? $asistencia->estado : 'falta';
                            $horaMarcado = $asistencia ? date('h:i A', strtotime($asistencia->hora_marcado)) : '--:--';
                            
                            $rowColor = [
                                'presente' => 'border-emerald-100 bg-emerald-50/20',
                                'tardanza' => 'border-amber-100 bg-amber-50/20',
                                'falta'    => 'border-gray-100 bg-white shadow-sm'
                            ][$estadoActual];
                        @endphp

                        <div class="flex flex-col md:flex-row items-center justify-between p-4 rounded-2xl border {{ $rowColor }} transition-all">
                            <!-- Info Alumno (Izquierda) -->
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center font-black text-indigo-600 text-xs border shadow-sm">
                                    {{ substr($alumno->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm uppercase leading-none">{{ $alumno->user->name }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 font-mono uppercase">{{ $alumno->dni }}</p>
                                </div>
                            </div>

                            <!-- Controles / Estado (Derecha) -->
                            <div class="mt-4 md:mt-0 flex items-center gap-4 w-full md:w-auto justify-end">
                                @if(!$esAlumno)
                                    <!-- Botones de Marcación para el Admin -->
                                    <div class="flex bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'presente')" 
                                            class="px-4 py-2 rounded-lg text-[10px] font-black transition-all {{ $estadoActual == 'presente' ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-300 hover:text-emerald-600' }}">
                                            PRESENTE
                                        </button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'tardanza')" 
                                            class="px-4 py-2 rounded-lg text-[10px] font-black transition-all {{ $estadoActual == 'tardanza' ? 'bg-amber-500 text-white shadow-lg' : 'text-gray-300 hover:text-amber-500' }}">
                                            TARDANZA
                                        </button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'falta')" 
                                            class="px-4 py-2 rounded-lg text-[10px] font-black transition-all {{ $estadoActual == 'falta' ? 'bg-rose-600 text-white shadow-lg' : 'text-gray-300 hover:text-rose-600' }}">
                                            FALTA
                                        </button>
                                    </div>
                                @else
                                    <!-- Vista de solo lectura para el Alumno -->
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-gray-400 uppercase leading-none">Marcado</p>
                                            <p class="text-xs font-bold text-gray-700 font-mono">{{ $horaMarcado }}</p>
                                        </div>
                                        <span class="px-6 py-2 rounded-xl text-[10px] font-black uppercase border
                                            {{ $estadoActual == 'presente' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                            {{ $estadoActual == 'tardanza' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                            {{ $estadoActual == 'falta' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}">
                                            {{ $estadoActual }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed">
                            <p class="text-gray-400 text-sm italic">No se encontraron alumnos registrados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        <!-- 5. PASO 4: FORMULARIO CREACIÓN -->
        @elseif($step == 4)
            <div class="max-w-lg mx-auto animate-fade-in">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-100 border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-600 p-8 text-center text-white">
                        <h3 class="text-xl font-black uppercase">Nueva Sesión</h3>
                        <p class="text-xs opacity-80 uppercase mt-1 tracking-widest">{{ $cicloSeleccionado['nombre'] }}</p>
                    </div>
                    <div class="p-10 space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block">Fecha de Clase</label>
                            <input type="date" wire:model="fecha" class="w-full rounded-2xl border-2 border-gray-50 bg-gray-50 p-4 font-bold outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block">Seleccionar Turno</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['mañana' => '🌅', 'tarde' => '☀️', 'noche' => '🌙'] as $val => $icon)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="turno" value="{{ $val }}" class="peer sr-only">
                                        <div class="p-4 text-center rounded-2xl border-2 border-gray-50 bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition-all">
                                            <span class="block text-xl">{{ $icon }}</span>
                                            <span class="block text-[10px] font-black uppercase">{{ $val }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button wire:click="goToStep(2)" class="flex-1 font-black uppercase text-xs text-gray-400 hover:text-gray-600">Cancelar</button>
                            <button wire:click="guardarControl" class="flex-[2] bg-indigo-600 text-white p-4 rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-indigo-700 active:scale-95 transition-all">
                                Iniciar Clase
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 6. MODAL DE CONFIRMACIÓN DE CIERRE -->
        @if($confirmandoCierre)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-[2.5rem] max-w-sm w-full shadow-2xl border border-gray-100 p-8 text-center">
                    <div class="inline-flex bg-rose-100 p-4 rounded-full text-rose-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Finalizar el Registro?</h3>
                    <p class="mt-4 text-sm text-gray-500 font-medium leading-relaxed">
                        Una vez cerrada la sesión, no se podrán realizar más cambios en las asistencias de hoy.
                    </p>
                    <div class="mt-8 flex gap-3">
                        <button wire:click="cerrarConfirmacionCierre" class="flex-1 px-6 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black uppercase text-[10px] hover:bg-gray-200 transition-all">Volver</button>
                        <button wire:click="cerrarControl" class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl hover:bg-rose-700 active:scale-95 transition-all">Sí, Finalizar</button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>