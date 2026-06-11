<div class="py-8 bg-gray-50 min-h-screen font-sans antialiased">
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
                        <button wire:click="cancel" class="ml-1 text-sm font-semibold {{ $view == 'index' ? 'text-indigo-600' : 'hover:text-indigo-600' }} md:ml-2 transition-colors">Docentes</button>
                    </div>
                </li>
                @if($view !== 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded md:ml-2">
                            {{ $view == 'create' ? 'Nuevo Registro' : ($view == 'edit' ? 'Editando Docente' : 'Ficha Docente') }}
                        </span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- 2. MENSAJES DE ESTADO -->
        @if (session()->has('message'))
            <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-sm rounded-r-xl flex justify-between items-center transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">&times;</button>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">

            <!-- VISTA: INDEX (LISTADO) -->
            @if($view == 'index')
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div class="relative w-full md:w-1/3 group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 focus:bg-white transition-all outline-none" 
                                   placeholder="Buscar por nombre o DNI...">
                        </div>
                        
                        <button wire:click="create" 
                                class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center justify-center active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nuevo Docente
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-50 rounded-2xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Docente / Email</th>
                                    <th class="px-6 py-4">DNI</th>
                                    <th class="px-6 py-4">Especialidad</th>
                                    <th class="px-6 py-4">Cursos Asignados</th>
                                    <th class="px-6 py-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($docentes as $docente)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-9 w-9 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-xs mr-3">
                                                    {{ strtoupper(substr($docente->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900 leading-tight group-hover:text-indigo-700">{{ $docente->user->name }}</div>
                                                    <div class="text-[11px] text-gray-400 font-medium italic">{{ $docente->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $docente->dni }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-blue-100">
                                                {{ $docente->especialidad }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1 max-w-[250px]">
                                                @foreach($docente->cursos->take(2) as $curso)
                                                    <span class="bg-white text-gray-500 text-[9px] px-2 py-0.5 rounded border border-gray-100 shadow-sm font-bold">
                                                        {{ $curso->nombre }}
                                                    </span>
                                                @endforeach
                                                @if($docente->cursos->count() > 2)
                                                    <span class="text-[9px] text-indigo-500 font-black">+{{ $docente->cursos->count() - 2 }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-1">
                                                <button wire:click="show({{ $docente->user_id }})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver Detalle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <button wire:click="edit({{ $docente->user_id }})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button onclick="confirm('¿Deseas eliminar a este docente?') || event.stopImmediatePropagation()" 
                                                        wire:click="delete({{ $docente->user_id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">No se encontraron docentes registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 px-2">{{ $docentes->links() }}</div>
                </div>

            <!-- VISTA: SHOW (DETALLES) -->
            @elseif($view == 'show')
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between border-b border-gray-100 pb-8 mb-8 gap-6">
                        <div class="flex items-center space-x-6">
                            <div class="h-20 w-20 bg-indigo-600 rounded-3xl flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-indigo-100">
                                {{ strtoupper(substr($selectedDocente->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">Perfil Profesional</span>
                                <h3 class="text-3xl font-black text-gray-800 leading-none">{{ $selectedDocente->user->name }}</h3>
                                <p class="text-gray-400 font-bold mt-2 flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $selectedDocente->user->email }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="cancel" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all text-sm active:scale-95">Volver al Listado</button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Columna Info -->
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Información del Docente</h4>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Especialidad Principal</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $selectedDocente->especialidad }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Documento de Identidad (DNI)</p>
                                        <p class="text-sm font-mono font-bold text-gray-800">{{ $selectedDocente->dni }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Teléfono de Contacto</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $selectedDocente->telefono ?? 'No registrado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Fecha de Contratación</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $selectedDocente->fecha_contratacion->format('d \d\e M, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Carga Académica -->
                        <div class="lg:col-span-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Cursos y Horarios Asignados</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @forelse($selectedDocente->cursos as $curso)
                                    <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:border-indigo-300 transition-colors group">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-black px-2 py-0.5 bg-blue-50 text-blue-600 rounded uppercase">{{ $curso->area->nombre }}</span>
                                        </div>
                                        <p class="font-black text-gray-800 group-hover:text-indigo-700 transition-colors leading-tight mb-2">{{ $curso->nombre }}</p>
                                        <div class="flex items-center text-[10px] text-gray-400 font-bold">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Ciclo: {{ $curso->ciclo->nombre ?? 'N/A' }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-12 border-2 border-dashed border-gray-100 rounded-3xl text-center text-gray-400 italic text-sm">
                                        No tiene cursos asignados en este periodo.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            <!-- VISTA: CREATE / EDIT -->
            @else
                <div class="p-8">
                    <form wire:submit.prevent="store" class="max-w-5xl mx-auto space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <!-- Datos Cuenta -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                    <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Cuenta de Usuario
                                </h3>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 ml-1">Nombre Completo</label>
                                    <input wire:model="name" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none">
                                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 ml-1">Email Académico</label>
                                    <input wire:model="email" type="email" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none">
                                    @error('email') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 ml-1">Contraseña</label>
                                    <input wire:model="password" type="password" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="••••••••">
                                    @error('password') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <!-- Perfil -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                    <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Perfil Profesional
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-gray-700 ml-1">DNI</label>
                                        <input wire:model="dni" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-mono">
                                        @error('dni') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-gray-700 ml-1">Teléfono</label>
                                        <input wire:model="telefono" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 ml-1">Especialidad</label>
                                    <input wire:model="especialidad" type="text" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none" placeholder="Ej: Ciencias Naturales">
                                    @error('especialidad') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 ml-1">Contratación</label>
                                    <input wire:model="fecha_contratacion" type="date" class="w-full p-3 bg-gray-50 border-2 border-gray-50 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none">
                                    @error('fecha_contratacion') <p class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cursos -->
                        <div class="mt-10 p-6 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-100">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Asignación de Carga Académica</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($allCursosGrouped as $area => $cursos)
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                        <h4 class="font-black text-indigo-700 text-[10px] uppercase mb-4 pb-2 border-b border-indigo-50">{{ $area }}</h4>
                                        <div class="space-y-3">
                                            @foreach($cursos as $curso)
                                                <label class="flex items-center group cursor-pointer">
                                                    <input type="checkbox" wire:model="selectedCursos" value="{{ $curso->id }}" class="w-5 h-5 rounded-lg border-gray-100 text-indigo-600">
                                                    <div class="ml-3">
                                                        <span class="text-xs font-bold text-gray-600">{{ $curso->nombre }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 pt-8 border-t border-gray-100">
                            <button type="button" wire:click="cancel" class="px-8 py-3 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest">Descartar</button>
                            <button type="submit" class="px-12 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 flex items-center">
                                <span wire:loading class="mr-3 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                Guardar Docente
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>