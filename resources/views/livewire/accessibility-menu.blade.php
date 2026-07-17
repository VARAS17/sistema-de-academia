<div x-data="{ open: false }" class="relative">
    <!-- Botón Principal: Resaltado en Azul -->
    <button @click="open = !open" 
            type="button"
            title="Abrir opciones de accesibilidad"
            class="w-full flex items-center justify-center lg:justify-start gap-3 p-3 rounded-xl 
                   bg-blue-600 text-white shadow-lg shadow-black/30
                   hover:bg-blue-500 hover:scale-[1.02] active:scale-95 
                   transition-all duration-200 group border border-blue-400/50"
            aria-label="Opciones de accesibilidad"
            aria-haspopup="true"
            :aria-expanded="open">
        
        <!-- Icono de Accesibilidad -->
        <div class="bg-white/20 text-white p-1.5 rounded-lg group-hover:bg-white group-hover:text-blue-600 transition-colors shadow-inner">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>

        <span class="text-xs font-bold uppercase tracking-widest hidden lg:inline drop-shadow-sm">
            Accesibilidad
        </span>

        <!-- Flecha indicadora -->
        <svg class="w-4 h-4 ml-auto hidden lg:inline opacity-70 transition-transform duration-200" 
             :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Modal / Menú Desplegable -->
    <div x-show="open"
         @click.outside="open = false"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-24 left-4 w-80 max-w-[95vw] bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-2xl rounded-2xl p-0 z-[9999] border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        
        <!-- Cabecera del Modal con botón cerrar -->
        <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 border-b dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h3 class="font-bold text-sm uppercase tracking-wider">Ajustes de Vista</h3>
            </div>
            <button @click="open = false" 
                    class="p-1 rounded-full hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                    title="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-6">
            <!-- Sección: Tamaño de Letra -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-xs uppercase text-zinc-500">Tamaño de letra</h3>
                    <button wire:click="resetFont" type="button"
                            class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        RESTABLECER
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click="decreaseFont" type="button"
                            class="flex-1 flex justify-center py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 transition-all active:scale-90">
                        <span class="text-sm font-bold">A-</span>
                    </button>
                    <div class="w-12 text-center font-mono font-bold text-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg py-1">
                        {{ $fontSize }}
                    </div>
                    <button wire:click="increaseFont" type="button"
                            class="flex-1 flex justify-center py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 transition-all active:scale-90">
                        <span class="text-sm font-bold">A+</span>
                    </button>
                </div>
            </div>

            <!-- Sección: Modo de Color -->
            <div>
                <h3 class="font-semibold text-xs uppercase text-zinc-500 mb-3 text-center lg:text-left">Modo daltónico / Contraste</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        'normal' => 'Normal',
                        'protanopia' => 'Protanopia',
                        'deuteranopia' => 'Deuteranopia',
                        'tritanopia' => 'Tritanopia',
                        'achromatopsia' => 'Acromático',
                    ] as $mode => $label)
                        <button wire:click="setColorMode('{{ $mode }}')" type="button"
                                class="text-[11px] font-bold px-2 py-2.5 rounded-lg border transition-all active:scale-95
                                {{ $colorMode === $mode
                                    ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20'
                                    : 'bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Botón de Ocultar/Finalizar (Clave para usuarios inexpertos) -->
            <button @click="open = false" 
                    type="button"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-black font-bold text-sm hover:opacity-90 transition-opacity mt-4 shadow-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                LISTO, CERRAR
            </button>
        </div>
    </div>
</div>