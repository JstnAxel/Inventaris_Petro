<div class="space-y-6">
    <form wire:submit.prevent="submit" class="space-y-4 mb-10">
        @foreach($requests as $index => $request)
            <div class="flex items-center gap-4">
                <select wire:model="requests.{{ $index }}.stationary_id" class="border rounded p-2" required>
                    <option value="">Pilih Alat Tulis</option>
                    @foreach($items as $item)
                        @if (!in_array($item->id, array_column($requests, 'stationary_id')) || $item->id == $request['stationary_id'])
                            <option class="text-black" value="{{ $item->id }}" data-stock="{{ $item->stock }}">{{ $item->name }} (stok: {{ $item->stock }})</option>
                        @endif
                    @endforeach
                </select>

                <input type="number" wire:model="requests.{{ $index }}.quantity" class="border rounded p-2 w-20" min="1" placeholder="Jumlah" />

        @if ($index > 0)
            <button type="button" wire:click="removeRow({{ $index }})" class="text-red-600 hover:underline">
                Hapus
            </button>
        @endif
            </div>
        @endforeach

        <button type="button" wire:click="addRow" class="bg-blue-500 text-white px-4 py-1 rounded">+ Tambah Item</button>

        <br>

<button type="button" onclick="confirmSubmit()" class="bg-green-600 text-white px-6 py-2 rounded">
    Kirim Permintaan
</button>

        @if (session()->has('success'))
            <div class="text-green-600 mt-2">{{ session('success') }}</div>
        @endif
    </form>

    <hr class="my-6">

    <h2 class="text-lg font-bold mb-2">Riwayat Permintaan Anda</h2>

    <table class="w-full border text-sm">
        <thead class="bg-gray-200 dark:bg-gray-700">
            <tr>
                <th class="border px-4 py-2 text-start">Tanggal</th>
                <th class="border px-4 py-2 text-start">Alat Tulis</th>
                <th class="border px-4 py-2 text-start">Jumlah</th>
                <th class="border px-4 py-2 text-start">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $request)
                <tr>
                    <td class="border px-4 py-2">{{ $request->created_at->format('d M Y') }}</td>
                    <td class="border px-4 py-2">{{ $request->stationary->name }}</td>
                    <td class="border px-4 py-2">{{ $request->quantity }}</td>
                    <td class="border px-4 py-2 capitalize">{{ $request->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Belum ada permintaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
function confirmSubmit() {
    let valid = true;
    let errorMessage = '';

    const selects = document.querySelectorAll('select[wire\\:model]');
    const quantities = document.querySelectorAll('input[type="number"][wire\\:model]');

    selects.forEach((select, index) => {
        const input = quantities[index];
        const quantity = parseInt(input.value);
        const selectedOption = select.options[select.selectedIndex];
        const stock = parseInt(selectedOption.getAttribute('data-stock'));

        // Validasi kosong
        if (!select.value) {
            valid = false;
            errorMessage = 'Silakan pilih semua alat tulis sebelum mengirim.';
            select.classList.add('border-red-500');
            return;
        } else {
            select.classList.remove('border-red-500');
        }

        // Validasi jumlah
        if (!quantity || quantity <= 0) {
            valid = false;
            errorMessage = 'Pastikan jumlah permintaan diisi dengan benar (min 1).';
            input.classList.add('border-red-500');
            return;
        } else {
            input.classList.remove('border-red-500');
        }

        // Validasi stok
        if (quantity > stock) {
            valid = false;
            errorMessage = `Jumlah melebihi stok yang tersedia (${stock}) untuk permintaan ke-${index + 1}.`;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if (!valid) {
        Swal.fire({
            title: 'Form Tidak Valid',
            text: errorMessage,
            icon: 'warning'
        });
        return;
    }

    Swal.fire({
        title: 'Kamu yakin?',
        text: "Data akan dikirim!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, kirim!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            @this.call('submit');
        }
    });
}
</script>
