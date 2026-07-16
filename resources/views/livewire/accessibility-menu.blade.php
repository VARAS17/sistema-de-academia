<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button"
            class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition"
            aria-label="Opciones de accesibilidad">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4" />
        </svg>
        <span class="sr-only">Accesibilidad</span>
    </button>

    <div x-show="open"
         @click.outside="open = false"
         x-cloak
         x-transition
         class="fixed bottom-20 left-4 w-72 max-w-[90vw] bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-2xl rounded-lg p-4 z-[9999] border border-zinc-200 dark:border-zinc-700">

        <h3 class="font-semibold mb-2 text-sm">Tamaño de letra</h3>
        <div class="flex items-center gap-2 mb-4">
            <button wire:click="decreaseFont" type="button"
                    class="px-3 py-1 rounded bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600">
                A-
            </button>
            <span class="text-xs px-2">{{ $fontSize }}</span>
            <button wire:click="increaseFont" type="button"
                    class="px-3 py-1 rounded bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600">
                A+
            </button>
            <button wire:click="resetFont" type="button"
                    class="ml-auto text-xs text-blue-600 dark:text-blue-400 hover:underline">
                Restablecer
            </button>
        </div>

        <h3 class="font-semibold mb-2 text-sm">Modo de color (daltonismo)</h3>
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                'normal' => 'Normal',
                'protanopia' => 'Protanopia',
                'deuteranopia' => 'Deuteranopia',
                'tritanopia' => 'Tritanopia',
                'achromatopsia' => 'Acromatopsia',
            ] as $mode => $label)
                <button wire:click="setColorMode('{{ $mode }}')" type="button"
                        class="text-xs px-2 py-1.5 rounded border transition
                        {{ $colorMode === $mode
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white dark:bg-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600 border-zinc-300 dark:border-zinc-600' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</div>