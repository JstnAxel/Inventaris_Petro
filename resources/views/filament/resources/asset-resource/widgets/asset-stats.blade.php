        @php
            $stats = $this->getStats();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-500">Total Assets</div>
                <div class="text-2xl font-bold text-primary">{{ $stats['total'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Available Asset</div>
                <div class="text-2xl font-bold text-success-600">{{ $stats['available'] }}</div>
            </x-filament::card>

        </div>
