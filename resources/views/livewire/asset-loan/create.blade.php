<div class="space-y-6">
    <form wire:submit.prevent="submit" class="space-y-4 mb-10">
        @foreach($rows as $index => $row)
            <div class="flex items-center gap-4">
                <select wire:model="rows.{{ $index }}.asset_id" class="border rounded p-2 " required>
                    <option value="">Pilih Asset</option>
                    @foreach($assets as $asset)
                        @if (!in_array($asset->id, array_column($rows, 'asset_id')) || $asset->id == $row['asset_id'])
                            <option class="text-black" value="{{ $asset->id }}">{{ $asset->name }}</option>
                        @endif
                    @endforeach
                </select>

                @if ($index > 0)
                    <button type="button" wire:click="removeRow({{ $index }})" class="text-red-600 hover:underline">
                        Hapus
                    </button>
                @endif
            </div>

            @error("rows.$index.asset_id")
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        @endforeach

        <button type="button" wire:click="addRow" class="bg-blue-500 text-white px-4 py-1 rounded">+ Tambah
            Asset</button>

        <br>

        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Ajukan Peminjaman</button>

        @if (session()->has('success'))
            <div class="text-green-600 mt-2">{{ session('success') }}</div>
        @endif
    </form>

    <hr class="my-6">

    <h2 class="text-lg font-bold mb-2">Riwayat Peminjaman Anda</h2>

    <table class="w-full border text-sm">
        <thead class="bg-gray-200 dark:bg-gray-700">
            <tr>
                <th class="border px-4 py-2 text-start">Tanggal</th>
                <th class="border px-4 py-2 text-start">Asset</th>
                <th class="border px-4 py-2 text-start">Status</th>
                <th class="border px-4 py-2 text-start">Kode Peminjaman</th> 
            </tr>
        </thead>
        <tbody>
            @forelse($history as $loan)
                <tr>
                    <td class="border px-4 py-2">{{ $loan->created_at->format('d M Y') }}</td>
                    <td class="border px-4 py-2">{{ $loan->asset->name }}</td>
                    <td class="border px-4 py-2 capitalize">{{ $loan->status }}</td>
                    <td class="border px-4 py-2">
                        @if($loan->status === 'approved')
                            {{ $loan->code }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Belum ada peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>