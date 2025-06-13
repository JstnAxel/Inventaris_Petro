<x-filament::page>
    <x-filament::card>
        <x-slot name="header">
            <h2 class="text-xl font-bold">Detail Asset: {{ $this->name }}</h2>
        </x-slot>
        {{ $this->table }}
    </x-filament::card>
</x-filament::page>
