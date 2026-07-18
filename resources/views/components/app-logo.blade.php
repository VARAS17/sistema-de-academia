@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand class="!w-full !max-w-none !px-0 !mx-0">
        <x-slot name="logo" class="flex items-center justify-center w-full overflow-visible">
            <img src="{{ asset('pacifico - copia.jpg') }}" alt="Pacífico" class="w-full h-auto object-contain">
        </x-slot>
    </flux:sidebar.brand>
@endif