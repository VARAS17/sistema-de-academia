<div class="p-4 md:p-8 bg-gray-50 min-h-screen font-sans antialiased text-gray-900">
    
    <!-- 1. SISTEMA DE BREADCRUMBS (Heurística: Reconocimiento antes que recuerdo) -->
    <nav class="flex mb-6 text-sm flex-wrap items-center text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 md:space-x-4">
            <li class="inline-flex items-center">
                <a href="#" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001.111 1H7v-6h6v6h1.889a1 1 0 001.111-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Inicio
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <button wire:click="showIndex" class="ml-1 md:ml-2 hover:text-indigo-600 transition-colors {{ $view == 'index' ? 'font-bold text-indigo-700' : '' }}">
                        Gestión de Alumnos
                    </button>
                </div>
            </li>
            @if($view != 'index')
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-xs uppercase tracking-tighter">
                            {{ $view == 'create' ? 'Nuevo Registro' : 'Editando Alumno' }}
                        </span>
                    </div>
                </li>
            @endif
        </ol>
    </nav>

    <!-- 2. MENSAJES DE ESTADO (Heurística: Visibilidad del estado del sistema) -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-white border-l-4 border-green-500 shadow-sm rounded-r-xl flex justify-between items-center transition-all animate-pulse">
            <div class="flex items-center text-green-700 font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('message') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>
    @endif

    <!-- 3. CONTENEDOR PRINCIPAL -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        
        <!-- HEADER DE LA TARJETA -->
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">
                    {{ $view == 'index' ? 'Gestión de Alumnos' : ($view == 'create' ? 'Registro de Estudiante' : 'Actualización de Perfil') }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $view == 'index' ? 'Visualiza, busca y administra la información de los estudiantes.' : 'Asegúrese de que los datos coincidan con el DNI oficial.' }}
                </p>
            </div>

            @if($view == 'index')
                <button wire:click="create" 
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 active:scale-95 transition-all shadow-lg shadow-indigo-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Añadir Alumno
                </button>
            @else
                <button wire:click="showIndex" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center group transition">
                    <svg class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver al listado
                </button>
            @endif
        </div>

        <!-- 4. CONTENIDO DINÁMICO -->
        <div class="p-6">
            @if($view == 'index')
                <!-- BUSCADOR CON INDICADOR DE CARGA -->
                <div class="mb-6 relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.400ms="search" type="text" 
                           placeholder="Buscar por nombre, correo o DNI..." 
                           class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all text-sm outline-none">
                    
                    <!-- Feedback visual de carga -->
                    <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>

                <!-- TABLA DE RESULTADOS (Heurística: Estética y diseño minimalista) -->
                <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-inner">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">DNI / Documento</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Carrera</th>
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
                                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter bg-blue-100 text-blue-700 rounded-lg border border-blue-200">
                                            {{ $alumno->carrera->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button wire:click="edit({{ $alumno->user_id }})" 
                                                    class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm group">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button onclick="confirm('¿Realmente desea eliminar este registro?') || event.stopImmediatePropagation()" 
                                                    wire:click="delete({{ $alumno->user_id }})" 
                                                    class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-gray-400 font-medium italic">No se encontraron estudiantes con ese criterio.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <div class="mt-6 px-2">
                    {{ $alumnos->links() }}
                </div>

            @else
                <!-- 5. FORMULARIO (Heurística: Prevención de errores) -->
                <form wire:submit.prevent="{{ $view == 'create' ? 'store' : 'update' }}" class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Columna Izquierda: Identidad -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Información Base
                            </h3>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Nombre Completo</label>
                                <input wire:model="name" type="text" 
                                       class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none @error('name') border-red-200 bg-red-50 @enderror">
                                @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Correo Electrónico</label>
                                <input wire:model="email" type="email" 
                                       class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none">
                                @error('email') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">DNI (Solo números)</label>
                                <input wire:model="dni" type="text" maxlength="8"
                                       class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-mono">
                                @error('dni') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Columna Derecha: Académico -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                <span class="bg-indigo-500 w-2 h-2 rounded-full mr-2"></span> Ubicación Académica
                            </h3>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Área de Estudios</label>
                                <select wire:model.live="area_id" 
                                        class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none cursor-pointer">
                                    <option value="">Seleccione un área...</option>
                                    @foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->nombre }}</option> @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700 {{ !$area_id ? 'text-gray-300' : '' }}">Ciclo</label>
                                <select wire:model="ciclo_id" 
                                        class="w-full p-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none disabled:opacity-50 disabled:cursor-not-allowed" 
                                        {{ !$area_id ? 'disabled' : '' }}>
                                    <option value="">Seleccione el ciclo...</option>
                                    @foreach($ciclos as $ciclo) <option value="{{ $ciclo->id }}">{{ $ciclo->nombre }}</option> @endforeach
                                </select>
                                @if(!$area_id)
                                    <p class="text-xs text-indigo-400 font-medium">← Primero elija un área de estudio.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- BOTONERA DE FORMULARIO -->
                    <div class="mt-12 flex flex-col sm:flex-row justify-end gap-4 border-t pt-8 border-gray-100">
                        <button type="button" wire:click="showIndex" 
                                class="px-8 py-3.5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all">
                            Descartar cambios
                        </button>
                        <button type="submit" 
                                class="px-10 py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center">
                            <span wire:loading.remove wire:target="store, update">
                                {{ $view == 'create' ? 'Crear Estudiante' : 'Guardar Cambios' }}
                            </span>
                            <span wire:loading wire:target="store, update" class="flex items-center italic">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    <!-- FOOTER DE AYUDA (Opcional) -->
    <div class="mt-8 text-center">
        <p class="text-xs text-gray-400 font-medium uppercase tracking-widest tracking-tighter">Sistema de Gestión Académica • v2.1</p>
    </div>
</div>