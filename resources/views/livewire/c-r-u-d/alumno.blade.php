<div class="py-1 bg-amber-50/40 dark:bg-amber-950/20 min-h-screen font-sans antialiased relative">
    <!-- 1. SISTEMA DE BREADCRUMBS -->
    <nav class="flex mb-6 text-sm flex-wrap items-center text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 md:space-x-4">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001.111 1H7v-6h6v6h1.889a1 1 0 001.111-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Inicio
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <button wire:click="cancel" class="ml-1 md:ml-2 hover:text-indigo-600 transition-colors {{ $view == 'index' ? 'font-bold text-indigo-700' : '' }}">
                        Gestión de Alumnos
                    </button>
                </div>
            </li>
            @if($view != 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-xs uppercase tracking-tighter">
                            @if($view == 'create') Nuevo Registro @elseif($view == 'edit') Editando Alumno @else Detalle @endif
                        </span>
                    </div>
                </li>
            @endif
        </ol>
    </nav>

    <!-- 2. MENSAJES DE ESTADO -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-white border-l-4 border-green-500 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
            <div class="flex items-center text-green-700 font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('message') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>
    @endif

    <!-- 3. CONTENEDOR PRINCIPAL -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">
                    @if($view == 'index') Gestión de Alumnos @elseif($view == 'show') Ficha del Estudiante @elseif($view == 'create') Registro de Estudiante @else Actualización de Perfil @endif
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $view == 'index' ? 'Visualiza, busca y administra la información de los estudiantes.' : 'Información personal y de identidad del alumno.' }}
                </p>
            </div>

            @if($view == 'index')
                <button wire:click="create" 
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 active:scale-95 transition-all shadow-lg shadow-indigo-200">
                    <div class="relative inline-flex items-center justify-center w-6 h-6 mr-2 flex-shrink-0">
                        <img src="{{ asset('metaforas/ESTUDIANTE.svg') }}?v={{ time() }}" class="w-full h-full object-contain">
                        <svg class="w-3.5 h-3.5 absolute -bottom-1 -right-1 bg-white rounded-full p-[1px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="#3B82F6" stroke="#0F172A" stroke-width="2.5" /><path d="M12 6v12M6 12h12" stroke="#0F172A" stroke-width="3" stroke-linecap="round" /></svg>
                    </div>
                    Añadir Alumno
                </button>
            @else
                <button wire:click="cancel" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition">
                    <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver al listado
                </button>
            @endif
        </div>

        <div class="p-6">
            @if($view == 'index')
                <!-- LISTADO -->
                <div class="mb-6 relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.400ms="search" type="text" 
                           placeholder="Buscar por nombre o DNI..." 
                           class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all text-sm outline-none">
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-inner">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">DNI / Documento</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Estado Actual</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @forelse($alumnos as $alumno)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-sm">
                                                {{ strtoupper(substr($alumno->user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 leading-tight">{{ $alumno->user->name }}</div>
                                                <div class="text-xs text-gray-400 font-medium">{{ $alumno->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600 bg-gray-50/40">{{ $alumno->dni }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($alumno->matriculas->last())
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter bg-green-100 text-green-700 rounded-lg border border-green-200">
                                                {{ $alumno->matriculas->last()->ciclo->nombre }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter bg-gray-100 text-gray-400 rounded-lg border border-gray-200">
                                                Sin Matrícula
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-1">
                                            <button wire:click="show({{ $alumno->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm relative inline-flex items-center justify-center" title="Ver Detalles">
                                                <img src="{{ asset('metaforas/ESTUDIANTE.svg') }}" class="w-10 h-10 object-contain">
                                                <svg class="w-4 h-4 absolute bottom-1.5 right-1.5 bg-white text-black rounded-full shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                            <button wire:click="edit({{ $alumno->id }})" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm group relative inline-flex items-center justify-center flex-shrink-0" title="Editar">
                                                <img src="{{ asset('metaforas/ESTUDIANTE.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                <svg class="w-[22px] h-[22px] absolute bottom-1 right-1 bg-white rounded-full p-[3px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g transform="rotate(45 12 12)" stroke="#1F2937" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"><path fill="#F87171" d="M9 5 C9 2, 15 2, 15 5 L15 7 L9 7 Z" /><rect fill="#E5E7EB" x="9" y="7" width="6" height="3" /><rect fill="#FACC15" x="9" y="10" width="6" height="7" /><line x1="12" y1="10" x2="12" y2="17" stroke="#EAB308" stroke-width="1.5" /><polygon fill="#FEF08A" points="9,17 15,17 12,21" /><polygon fill="#1F2937" points="11.25,20 12.75,20 12,21" /></g></svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $alumno->id }})" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm relative inline-flex items-center justify-center flex-shrink-0" title="Eliminar">
                                                <img src="{{ asset('metaforas/ESTUDIANTE.svg') }}?v={{ time() }}" class="w-10 h-10 object-contain flex-shrink-0">
                                                <svg class="w-[22px] h-[22px] absolute bottom-1 right-1 bg-white rounded-full p-[2px] shadow-sm border border-gray-100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5V3.5C9 3 9.5 2.5 10 2.5h4c.5 0 1 .5 1 1.5V5" fill="none" /><rect x="4" y="5" width="16" height="3" rx="1" fill="#FCA5A5" /><path d="M5.5 8l1.5 13.5c.1.8.8 1.5 1.5 1.5h7c.8 0 1.4-.7 1.5-1.5L18.5 8" fill="#FEE2E2" /><line x1="8.5" y1="11" x2="9.5" y2="18" /><line x1="12" y1="11" x2="12" y2="18" /><line x1="15.5" y1="11" x2="14.5" y2="18" /><circle cx="17" cy="17" r="6" fill="#FCA5A5" /><path d="M14.5 14.5l5 5m0-5l-5 5" /></g></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">No se encontraron estudiantes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 px-2">{{ $alumnos->links() }}</div>

            @elseif($view == 'show')
                <!-- VISTA DETALLES (SHOW) -->
                <div class="max-w-5xl mx-auto">
                    <div class="flex flex-col md:flex-row items-center gap-8 mb-10 border-b border-gray-50 pb-10">
                        <div class="h-24 w-24 bg-indigo-600 text-white rounded-3xl flex items-center justify-center text-4xl font-black shadow-2xl shadow-indigo-100">
                            {{ strtoupper(substr($selectedAlumno->user->name, 0, 1)) }}
                        </div>
                        <div class="text-center md:text-left">
                            <h2 class="text-3xl font-black text-gray-800 leading-tight">{{ $selectedAlumno->user->name }}</h2>
                            <p class="text-indigo-500 font-bold flex items-center justify-center md:justify-start mt-2 uppercase tracking-widest text-xs">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Ficha de Alumno - DNI: {{ $selectedAlumno->dni }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 border-b pb-2">Información de Contacto</h4>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Email Principal</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $selectedAlumno->user->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Teléfono / Celular</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $selectedAlumno->telefono ?? 'No registrado' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Historial de Matrículas</h4>
                            <div class="space-y-4">
                                @forelse($selectedAlumno->matriculas as $mat)
                                    <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm flex items-center justify-between hover:border-indigo-300 transition-colors">
                                        <div class="flex items-center">
                                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl mr-4">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-gray-800 leading-tight">{{ $mat->ciclo->nombre }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $mat->created_at->format('d M, Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-[9px] font-black uppercase rounded-lg {{ $mat->estado == 'Activa' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                            {{ $mat->estado }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-400 italic text-sm">
                                        Este alumno aún no registra ninguna matrícula académica.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- FORMULARIO CREATE / EDIT (SOLO IDENTIDAD) -->
                <form wire:submit.prevent="{{ $view == 'create' ? 'store' : 'update' }}" class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Columna 1 -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Datos Personales
                            </h3>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 ml-1">Nombre Completo</label>
                                <input wire:model="name" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="Ej. Juan Pérez">
                                @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 ml-1">DNI (Documento de Identidad)</label>
                                <!-- MODIFICACIÓN: Agregado maxlength 8 y sugerencia de solo números -->
                                <input wire:model="dni" type="text" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-mono" placeholder="8 dígitos exactos">
                                @error('dni') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 ml-1">Teléfono / Celular</label>
                                <!-- MODIFICACIÓN: Agregado maxlength 9 y placeholder de ejemplo que empieza con 9 -->
                                <input wire:model="telefono" type="text" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="Ej. 9XXXXXXXX">
                                @error('telefono') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Credenciales de Acceso
                            </h3>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 ml-1">Correo Electrónico</label>
                                <input wire:model="email" type="email" class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="usuario@ejemplo.com">
                                @error('email') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 ml-1">Contraseña {{ $view == 'edit' ? '(Dejar vacío para mantener)' : '' }}</label>
                                <input wire:model="password" type="password" class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="Mínimo 6 caracteres">
                                @error('password') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex flex-col sm:flex-row justify-end gap-4 border-t pt-8 border-gray-100">
                        <button type="button" wire:click="cancel" class="px-8 py-3 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Descartar</button>
                        <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 flex items-center justify-center transition-all active:scale-95">
                            <span wire:loading.remove wire:target="store, update">{{ $view == 'create' ? 'Crear Ficha de Alumno' : 'Actualizar Información' }}</span>
                            <span wire:loading wire:target="store, update" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> 
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- MODAL DE ELIMINACIÓN -->
    @if($alumnoIdBeingDeleted)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('alumnoIdBeingDeleted', null)"></div>

        <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl transform transition-all border border-gray-100 z-[110] overflow-hidden">
            <div class="p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-red-50 border border-red-100">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">¿Eliminar Alumno?</h3>
                        <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">
                            Esta acción eliminará permanentemente al estudiante y su historial académico. Esta operación no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-4 flex flex-col sm:flex-row-reverse gap-3 mt-2">
                <button wire:click="delete" type="button" class="inline-flex justify-center rounded-xl px-8 py-3 bg-red-600 text-sm font-black text-white hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-100">
                    Confirmar Eliminación
                </button>
                <button wire:click="$set('alumnoIdBeingDeleted', null)" type="button" class="inline-flex justify-center rounded-xl border border-gray-200 px-8 py-3 bg-white text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>