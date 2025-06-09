        @php
            $stats = $this->getStats();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-500">Loaned Asset</div>
                <div class="text-2xl font-bold text-warning-600">{{ $stats['loaned'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Maintenance Asset</div>
                <div class="text-2xl font-bold text-danger-600">{{ $stats['maintenance'] }}</div>
            </x-filament::card>
        </div>
