<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Card General Info --}}
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">General Information</h2>
            <div><strong>Code:</strong> {{ $record->code }}</div>
            <div><strong>Name:</strong> {{ $record->name }}</div>
            <div><strong>Category:</strong> {{ $record->category?->name }}</div>
            <div><strong>Status:</strong>
                <x-filament::badge :color="match($record->status) {
                    'available' => 'success',
                    'loaned' => 'warning',
                    'maintenance' => 'danger',
                    default => 'gray',
                }">
                    {{ ucfirst($record->status) }}
                </x-filament::badge>
            </div>
            <div class="mt-2"><strong>Note:</strong><br>{{ $record->note }}</div>
        </x-filament::card>

        {{-- Card Image --}}
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">Image</h2>
            @if($record->image)
                <img src="{{ asset('storage/' . $record->image) }}" class="w-full h-auto"/>
            @else
                <p class="text-gray-500 italic">No image available.</p>
            @endif
        </x-filament::card>

        {{-- Card Metadata --}}
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">Metadata</h2>
            <div><strong>Created At:</strong> {{ $record->created_at->format('d M Y H:i') }}</div>
            <div><strong>Updated At:</strong> {{ $record->updated_at->format('d M Y H:i') }}</div>
        </x-filament::card>

    </div>
</x-filament::page>
