<div class="bg-amber-50/40 min-h-screen font-sans antialiased pb-20">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8">
        
        <!-- 1. SISTEMA DE BREADCRUMBS -->
        <nav class="flex mb-8 text-sm items-center text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
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
                            <button wire:click="goToStep({{ $item['step'] }})" 
                                class="ml-1 md:ml-2 {{ $loop->last ? 'text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100' : 'hover:text-indigo-600 font-medium' }} uppercase text-[10px] tracking-tight transition-all">
                                {{ $item['name'] }}
                            </button>
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
            <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
                <!-- Header del Ciclo -->
                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 text-white flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-[10px] font-black opacity-70 uppercase tracking-[0.2em] mb-1">Registro de Asistencias</p>
                        <h3 class="text-2xl font-black uppercase tracking-tight">{{ $cicloSeleccionado['nombre'] }}</h3>
                        <p class="text-xs font-medium opacity-80 italic">{{ $areaSeleccionada['nombre'] ?? '' }}</p>
                    </div>
                    @if(!$esAlumno)
                        <button wire:click="mostrarFormularioCreacion" class="h-14 px-6 flex items-center justify-center gap-2 bg-[#98FB98] text-black font-black uppercase text-[10px] rounded-2xl hover:bg-[#7FE67F] transition-all shadow-lg active:scale-95">
                            <img src="{{ asset('meta-register/asistencia.png') }}"
                                alt="Nueva Sesión"
                                class="w-12 h-12 object-contain">
                            Registrar Asistencia    
                        </button>
                    @endif
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha y Turno</th>
                                @if($esAlumno)
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mi Asistencia</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hora Marcado</th>
                                @else
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($controles as $ctrl)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-700 font-mono">{{ date('d/m/Y', strtotime($ctrl->fecha)) }}</p>
                                        <p class="text-[9px] font-black uppercase text-gray-400 tracking-tighter">{{ $ctrl->turno }}</p>
                                    </td>

                                    @if($esAlumno)
                                        @php 
                                            $marcacion = $ctrl->asistencias->where('alumno_id', $alumnoPerfil->user_id)->first();
                                        @endphp
                                        <td class="px-6 py-4">
                                            @if($marcacion)
                                                <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase 
                                                    {{ $marcacion->estado == 'presente' ? 'bg-emerald-100 text-emerald-700' : ($marcacion->estado == 'tardanza' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                                    {{ $marcacion->estado }}
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-300 italic">Sin registro</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-500 font-mono">
                                            {{ $marcacion ? date('h:i A', strtotime($marcacion->hora_marcado)) : '--:--' }}
                                        </td>
                                    @else
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $ctrl->estado == 'abierto' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $ctrl->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end items-center gap-2">

                                                <!-- BOTÓN VER -->
                                                <button wire:click="verAsistencia({{ $ctrl->id }})"
                                                    class="p-2 hover:bg-indigo-100 rounded-xl transition-all"
                                                    title="Ver asistencia">

                                                    <img src="{{ asset('meta-ver/asistencia.jpeg') }}"
                                                        alt="Ver"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                                <!-- BOTÓN EDITAR (Bloqueado si está cerrado) -->
                                                @if($ctrl->estado == 'abierto')
                                                    <button wire:click="editarAsistencia({{ $ctrl->id }})"
                                                        class="p-2 hover:bg-amber-100 rounded-xl transition-all"
                                                        title="Editar asistencia">

                                                        <img src="{{ asset('meta-editar/asistencia.jpeg') }}"
                                                            alt="Editar"
                                                            class="w-12 h-12 object-contain">
                                                    </button>
                                                @else
                                                    <button disabled
                                                        class="p-2 text-gray-300 cursor-not-allowed"
                                                        title="Sesión cerrada">

                                                        <!-- Se mantiene el candado -->
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- BOTÓN ELIMINAR -->
                                                <button wire:click="abrirConfirmacionEliminacion({{ $ctrl->id }})"
                                                    class="p-2 hover:bg-rose-100 rounded-xl transition-all"
                                                    title="Eliminar asistencia">

                                                    <img src="{{ asset('meta-eliminar/asistencia.jpeg') }}"
                                                        alt="Eliminar"
                                                        class="w-12 h-12 object-contain">
                                                </button>

                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- 4. PASO 3: PANEL DE MARCACIÓN (VER O EDITAR) -->
        @elseif($step == 3)
            <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6 border-b border-gray-50 pb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">
                            {{ $modoLectura ? 'Vista de' : 'Editar' }} Asistencia
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ date('d/m/Y', strtotime($controlSeleccionado->fecha)) }}</span>
                            <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                            <span class="text-[10px] font-black text-indigo-500 uppercase italic">{{ $controlSeleccionado->turno }}</span>
                        </div>
                    </div>

                    @if(!$modoLectura && $controlSeleccionado->estado == 'abierto')
                        <button wire:click="$set('confirmandoCierre', true)" class="bg-rose-600 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-rose-100 hover:bg-rose-700 transition-all">
                            Finalizar Registro del Día
                        </button>
                    @endif
                </div>

                <div class="relative mb-6">
                    <input type="text" wire:model.live="search" placeholder="Buscar por Nombre o DNI..." class="w-full pl-10 p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                    <svg class="w-5 h-5 absolute left-3 top-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="space-y-3">
                    @foreach($alumnos as $alumno)
                        @php
                            $asistencia = $alumno->asistencias->first();
                            $estadoActual = $asistencia ? $asistencia->estado : 'falta';
                            $horaMarcado = $asistencia ? date('h:i A', strtotime($asistencia->hora_marcado)) : '--:--';
                        @endphp

                        <div class="flex flex-col md:flex-row items-center justify-between p-4 rounded-2xl border transition-all {{ $estadoActual == 'presente' ? 'bg-emerald-50/30 border-emerald-100' : ($estadoActual == 'tardanza' ? 'bg-amber-50/30 border-amber-100' : 'bg-white border-gray-100') }}">
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center font-black text-indigo-600 text-xs border shadow-sm">
                                    {{ substr($alumno->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm uppercase">{{ $alumno->user->name }}</h4>
                                    <p class="text-[10px] text-gray-400 font-mono uppercase">{{ $alumno->dni }}</p>
                                </div>
                            </div>

                            <div class="mt-4 md:mt-0 flex items-center gap-6 w-full md:w-auto justify-end">
                                <div class="text-right">
                                    <p class="text-[8px] font-black text-gray-300 uppercase leading-none">Hora</p>
                                    <p class="text-xs font-bold text-gray-600 font-mono">{{ $horaMarcado }}</p>
                                </div>

                                @if(!$modoLectura)
                                    <div class="flex bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'presente')" 
                                            class="px-4 py-2 rounded-lg text-[9px] font-black transition-all {{ $estadoActual == 'presente' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:text-emerald-600' }}">
                                            P
                                        </button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'tardanza')" 
                                            class="px-4 py-2 rounded-lg text-[9px] font-black transition-all {{ $estadoActual == 'tardanza' ? 'bg-amber-500 text-white shadow-md' : 'text-gray-300 hover:text-amber-500' }}">
                                            T
                                        </button>
                                        <button wire:click="marcarAsistencia({{ $alumno->user_id }}, 'falta')" 
                                            class="px-4 py-2 rounded-lg text-[9px] font-black transition-all {{ $estadoActual == 'falta' ? 'bg-rose-600 text-white shadow-md' : 'text-gray-300 hover:text-rose-600' }}">
                                            F
                                        </button>
                                    </div>
                                @else
                                    <span class="px-5 py-2 rounded-xl text-[10px] font-black uppercase border
                                        {{ $estadoActual == 'presente' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                        {{ $estadoActual == 'tardanza' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                        {{ $estadoActual == 'falta' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}">
                                        {{ $estadoActual }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
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
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block tracking-widest">Fecha de Clase</label>
                            <input type="date" wire:model="fecha" class="w-full rounded-2xl border-2 border-gray-50 bg-gray-50 p-4 font-bold outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block tracking-widest">Seleccionar Turno</label>
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
                            <button wire:click="goToStep(2)" class="flex-1 font-black uppercase text-xs text-gray-400 hover:text-gray-600 transition-colors">Cancelar</button>
                            <button wire:click="guardarControl" class="flex-[2] bg-indigo-600 text-white p-4 rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-indigo-700 active:scale-95 transition-all">
                                Iniciar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
        @if($confirmandoEliminacion)
            <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-[2.5rem] max-w-sm w-full shadow-2xl p-8 text-center border border-gray-100">
                    <div class="inline-flex bg-rose-100 p-4 rounded-full text-rose-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Eliminar Sesión?</h3>
                    <p class="mt-4 text-sm text-gray-500 font-medium leading-relaxed">Esta acción es irreversible. Se borrarán todos los registros de asistencia de esta fecha.</p>
                    <div class="mt-8 flex gap-3">
                        <button wire:click="$set('confirmandoEliminacion', false)" class="flex-1 px-6 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black uppercase text-[10px] hover:bg-gray-200 transition-all">Cancelar</button>
                        <button wire:click="eliminarControl" class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl hover:bg-rose-700 transition-all">Sí, Eliminar</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL DE CIERRE (FINALIZAR DÍA) -->
        @if($confirmandoCierre)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-[2.5rem] max-w-sm w-full shadow-2xl p-8 text-center border border-gray-100">
                    <div class="inline-flex bg-amber-100 p-4 rounded-full text-amber-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Cerrar Sesión?</h3>
                    <p class="mt-4 text-sm text-gray-500 font-medium leading-relaxed">Una vez cerrada, no podrás modificar las asistencias de hoy a menos que contactes con soporte.</p>
                    <div class="mt-8 flex gap-3">
                        <button wire:click="$set('confirmandoCierre', false)" class="flex-1 px-6 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black uppercase text-[10px] hover:bg-gray-200 transition-all">Volver</button>
                        <button wire:click="cerrarControl" class="flex-[2] px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl hover:bg-indigo-700 transition-all">Finalizar Ahora</button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>