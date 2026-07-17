<flux:dropdown position="top" align="start" class="w-full">
    <!-- Gatillo del Menú (El botón que se ve en el sidebar) -->
    <flux:sidebar.profile
        :name="auth()->user()->name"
        :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down"
        class="w-full !bg-blue-600 !text-white [&_*:not([data-flux-avatar]):not([data-flux-avatar]_*)]:!text-white border border-blue-400/50 shadow-lg shadow-black/20 rounded-xl hover:!bg-blue-500 transition-all active:scale-95 cursor-pointer p-2"
        data-test="sidebar-menu-button"
    />

    <flux:menu class="w-80 p-0 overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-2xl rounded-2xl">
        <!-- Cabecera solo con botón cerrar, esquina superior izquierda, rojo -->
        <div class="flex items-center justify-start bg-zinc-50 dark:bg-zinc-800/50 px-2 py-2 border-b dark:border-zinc-700">
            <flux:menu.item class="!p-1.5 !rounded-full !flex-none !text-red-500 hover:!bg-red-50 dark:hover:!bg-red-950/30">
                <flux:icon.x-mark class="w-4 h-4" />
            </flux:menu.item>
        </div>

        <!-- Opciones del Menú -->
        <div class="p-2 bg-white dark:bg-zinc-900 space-y-1">
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="rounded-lg font-medium">
                {{ __('Configuración del Perfil') }}
            </flux:menu.item>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer rounded-lg font-medium !text-red-500 hover:!bg-red-50 dark:hover:!bg-red-950/30"
                    data-test="logout-button"
                >
                    {{ __('Cerrar Sesión') }}
                </flux:menu.item>
            </form>
        </div>

        <flux:menu.separator class="m-0" />

        <!-- Botón de salida explícito para usuarios inexpertos -->
        <div class="p-2 bg-zinc-50 dark:bg-zinc-800/30">
            <button
                type="button"
                x-on:click="$dispatch('close')"
                class="flex justify-center items-center gap-2 py-2.5 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-black font-bold text-xs uppercase tracking-tighter transition-opacity w-full hover:opacity-90 hover:!bg-zinc-900 dark:hover:!bg-white hover:!text-white dark:hover:!text-black"
            >
                <flux:icon.check class="w-3 h-3" />
                {{ __('Listo, cerrar menú') }}
            </button>
        </div>
    </flux:menu>
</flux:dropdown>