<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stationaries as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td>{{ $item->stockHistories()->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('amount') }}</td>
                <td>{{ $item->stationeryRequests()->where('status', 'approved')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('quantity') }}</td>
                <td>{{ $item->stock }} {{ $item->unit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
