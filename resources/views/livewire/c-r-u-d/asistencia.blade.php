<div class="bg-amber-50/40 min-h-screen font-sans antialiased relative px-4 py-6">
    
    <!-- 1. SISTEMA DE BREADCRUMBS -->
    <nav class="flex mb-6 px-4 py-3 text-gray-500 bg-white shadow-sm border border-gray-100 rounded-xl" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Inicio
                </a>
            </li>
            @foreach($breadcrumb as $item)
                <li aria-current="{{ $loop->last ? 'page' : 'false' }}">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <button wire:click="goToStep({{ $item['step'] }})"
                            class="ml-1 text-sm font-semibold {{ $loop->last ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors uppercase tracking-tighter">
                            {{ $item['name'] }}
                        </button>
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- MENSAJES DE ESTADO -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-white border-l-4 border-green-500 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
            <div class="flex items-center text-green-700 font-medium text-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('message') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>
    @endif

    <!-- 2. CONTENEDOR PRINCIPAL -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        
        <!-- CABECERA DINÁMICA DEL CONTENEDOR -->
        <div class="p-6 border-b border-gray-50 bg-white flex flex-col sm:flex-row justify-between items-center gap-4">
            @if($step == 1)
                <div>
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Seleccionar Ciclo Académico</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Elija un ciclo para gestionar las asistencias</p>
                </div>
            @elseif($step == 2)
                <div class="flex flex-col gap-2">
                    <!-- BOTÓN REGRESAR A PASO 1 -->
                    <button wire:click="goToStep(1)" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition w-fit">
                        <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Volver a Ciclos
                    </button>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('metaforas/asistencia.jpeg') }}"
                                alt="Calendario"
                                class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase leading-tight">{{ $cicloSeleccionado['nombre'] }}</h3>
                            <p class="text-[10px] text-indigo-500 font-bold uppercase">{{ $areaSeleccionada['nombre'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                @if(!$esAlumno)
                    <button wire:click="mostrarFormularioCreacion" class="h-14 px-6 flex items-center bg-[#98FB98] text-black font-black uppercase text-[10px] rounded-xl hover:bg-[#7FE67F] transition shadow-lg active:scale-95">
                        <img src="{{ asset('meta-register/asistencia.png') }}" alt="Nuevo" class="w-10 h-10 mr-2 object-contain">
                        Registrar Nueva Asistencia
                    </button>
                @endif
            @elseif($step == 3)
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full justify-between">
                    <button wire:click="goToStep(2)" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition">
                        <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Volver a Sesiones
                    </button>
                    <div class="text-center">
                        <h2 class="text-lg font-black text-gray-800 uppercase">{{ $modoLectura ? 'Vista' : 'Marcación' }} de Asistencia</h2>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase">{{ date('d/m/Y', strtotime($controlSeleccionado->fecha)) }} - {{ $controlSeleccionado->turno }}</p>
                    </div>
                    @if(!$modoLectura && $controlSeleccionado->status == 'abierto')
                        <button wire:click="$set('confirmandoCierre', true)" class="px-6 py-3 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-rose-700 transition-all">Finalizar Día</button>
                    @else
                        <div class="w-32"></div> <!-- Spacer -->
                    @endif
                </div>
            @elseif($step == 4)
                <button type="button" wire:click="goToStep(2)" class="px-8 py-3 text-sm font-bold text-red-600 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 hover:text-red-700 active:scale-95 transition-all duration-150 uppercase tracking-widest shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Cancelar
                </button>
                <h2 class="text-lg font-black text-gray-800 uppercase">Nueva Sesión de Asistencia</h2>
            @endif
        </div>

        <!-- CUERPO DINÁMICO DEL CONTENEDOR -->
        <div class="p-6">
            
            <!-- PASO 1: SELECCIÓN DE CICLO -->
            @if($step == 1 && !$esAlumno)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
                    @foreach($ciclos as $ciclo)
                        <button wire:click="seleccionarCiclo({{ $ciclo->id }})" class="bg-white rounded-2xl border-2 border-gray-100 p-6 hover:border-indigo-500 hover:shadow-xl transition-all group text-left active:scale-95">
                            <h3 class="font-black text-indigo-600 text-lg uppercase mb-2 group-hover:scale-105 transition-transform">{{ $ciclo->nombre }}</h3>
                            <div class="flex justify-between items-center">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $ciclo->area->nombre }}</p>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </button>
                    @endforeach
                </div>

            <!-- PASO 2: LISTADO DE SESIONES -->
            @elseif($step == 2)
                <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm animate-fade-in">
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
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-800">{{ date('d/m/Y', strtotime($ctrl->fecha)) }}</p>
                                        <p class="text-[9px] font-black uppercase text-indigo-400">{{ $ctrl->turno }}</p>
                                    </td>
                                    @if($esAlumno)
                                        @php $marcacion = $ctrl->asistencias->where('alumno_id', $alumnoPerfil->user_id)->first(); @endphp
                                        <td class="px-6 py-4">
                                            @if($marcacion)
                                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase {{ $marcacion->estado == 'presente' ? 'bg-green-100 text-green-700' : ($marcacion->estado == 'tardanza' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ $marcacion->estado }}
                                                </span>
                                            @else
                                                <span class="text-[10px] text-gray-300 italic">No registrado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono font-bold text-gray-500">{{ $marcacion ? date('h:i A', strtotime($marcacion->hora_marcado)) : '--:--' }}</td>
                                    @else
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase {{ $ctrl->estado == 'abierto' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                                {{ $ctrl->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-1">
                                                <button wire:click="verAsistencia({{ $ctrl->id }})" class="p-1 hover:bg-indigo-100 rounded-lg transition-colors">
                                                    <img src="{{ asset('meta-ver/asistencia.jpeg') }}" alt="Ver" class="w-10 h-10 object-contain">
                                                </button>
                                                @if($ctrl->estado == 'abierto')
                                                    <button wire:click="editarAsistencia({{ $ctrl->id }})" class="p-1 hover:bg-amber-100 rounded-lg transition-colors">
                                                        <img src="{{ asset('meta-editar/asistencia.jpeg') }}" alt="Editar" class="w-10 h-10 object-contain">
                                                    </button>
                                                @endif
                                                <button wire:click="abrirConfirmacionEliminacion({{ $ctrl->id }})" class="p-1 hover:bg-red-100 rounded-lg transition-colors">
                                                    <img src="{{ asset('meta-eliminar/asistencia.jpeg') }}" alt="Eliminar" class="w-10 h-10 object-contain">
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            <!-- PASO 3: PANEL DE MARCACIÓN -->
            @elseif($step == 3)
                <div class="animate-fade-in">
                    <div class="relative mb-6">
                        <input type="text" wire:model.live="search" placeholder="Buscar por Nombre o DNI..." class="w-full pl-12 p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-500 outline-none transition-all font-bold text-sm">
                        <svg class="w-5 h-5 absolute left-4 top-4.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <div class="space-y-3">
                        @foreach($alumnos as $alumno)
                            @php
                                $asistencia = $alumno->asistencias->first();
                                $estadoActual = $asistencia ? $asistencia->estado : 'falta';
                            @endphp
                            <div class="flex flex-col sm:flex-row items-center justify-between p-4 rounded-2xl border-2 {{ $estadoActual == 'presente' ? 'border-green-100 bg-green-50/30' : ($estadoActual == 'tardanza' ? 'border-amber-100 bg-amber-50/30' : 'border-gray-50 bg-white') }} transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-xs">
                                        {{ substr($alumno->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm uppercase">{{ $alumno->user->name }}</h4>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $alumno->dni }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 mt-4 sm:mt-0">
                                    <div class="text-right">
                                        <p class="text-[8px] font-black text-gray-300 uppercase leading-none">Marcado</p>
                                        <p class="text-xs font-bold text-gray-600 font-mono">{{ $asistencia ? date('h:i A', strtotime($asistencia->hora_marcado)) : '--:--' }}</p>
                                    </div>
                                    @if(!$modoLectura)
                                        <div class="flex bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                                            @foreach(['presente' => 'P', 'tardanza' => 'T', 'falta' => 'F'] as $val => $label)
                                                <button wire:click="marcarAsistencia({{ $alumno->user_id }}, '{{ $val }}')" 
                                                    class="px-4 py-2 rounded-lg text-[9px] font-black transition-all {{ $estadoActual == $val ? ($val=='presente'?'bg-green-600 text-white':($val=='tardanza'?'bg-amber-500 text-white':'bg-red-600 text-white')) : 'text-gray-300 hover:bg-gray-50' }}">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="px-5 py-2 rounded-xl text-[10px] font-black uppercase border {{ $estadoActual == 'presente' ? 'bg-green-100 text-green-700' : ($estadoActual == 'tardanza' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $estadoActual }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            <!-- PASO 4: FORMULARIO CREACIÓN -->
            @elseif($step == 4)
                <div class="max-w-lg mx-auto py-10 animate-fade-in">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block tracking-widest">Fecha de Registro</label>
                            <input type="date" wire:model="fecha" class="w-full rounded-2xl border-2 border-gray-100 bg-gray-50 p-4 font-bold outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-2 mb-2 block tracking-widest">Seleccionar Turno</label>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach(['mañana' => '🌅 MAÑANA', 'tarde' => '☀️ TARDE'] as $val => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="turno" value="{{ $val }}" class="peer sr-only">
                                        <div class="p-5 text-center rounded-2xl border-2 border-gray-100 bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition-all font-black text-[10px] uppercase">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="pt-6">
                            <button wire:click="guardarControl" class="w-full h-14 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-indigo-700 active:scale-95 transition-all">
                                Abrir Sesión de Asistencia
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODALES DE ACCIÓN -->
    @if($confirmandoEliminacion)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] max-w-sm w-full shadow-2xl p-8 text-center border border-gray-100">
                <div class="inline-flex bg-red-50 text-red-600 p-4 rounded-3xl mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight mb-2">¿Eliminar Sesión?</h3>
                <p class="text-gray-400 text-sm mb-8 font-medium italic">Esta acción borrará permanentemente todas las asistencias de este día.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('confirmandoEliminacion', false)" class="flex-1 px-6 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase text-[10px]">Cancelar</button>
                    <button wire:click="eliminarControl" class="flex-[2] px-6 py-4 bg-red-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl hover:bg-red-700">Confirmar</button>
                </div>
            </div>
        </div>
    @endif

    @if($confirmandoCierre)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] max-w-sm w-full shadow-2xl p-8 text-center border border-gray-100">
                <div class="inline-flex bg-amber-50 text-amber-600 p-4 rounded-3xl mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight mb-2">¿Finalizar Día?</h3>
                <p class="text-gray-400 text-sm mb-8 font-medium italic">Una vez cerrada, la sesión ya no podrá ser editada.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('confirmandoCierre', false)" class="flex-1 px-6 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase text-[10px]">Volver</button>
                    <button wire:click="cerrarControl" class="flex-[2] px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-xl">Finalizar</button>
                </div>
            </div>
        </div>
    @endif

</div>