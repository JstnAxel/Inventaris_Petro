<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Total</th>
            <th>Status</th>
            <th>Dibuat</th>
            <th>Diupdate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->name }}</td>
                <td>{{ \App\Models\Asset::where('name', $asset->name)->count() }}</td>
                <td>
                    @php
                        $status = 'unknown';
                        if (\App\Models\Asset::where('name', $asset->name)->where('status', 'available')->exists()) {
                            $status = 'available';
                        } elseif (\App\Models\Asset::where('name', $asset->name)->where('status', 'maintenance')->exists()) {
                            $status = 'maintenance';
                        } elseif (\App\Models\Asset::where('name', $asset->name)->where('status', 'loaned')->exists()) {
                            $status = 'loaned';
                        }
                    @endphp
                    {{ $status }}
                </td>
                <td>{{ optional(\App\Models\Asset::where('name', $asset->name)->orderBy('created_at')->first())->created_at?->format('d M Y H:i') }}</td>
                <td>{{ optional(\App\Models\Asset::where('name', $asset->name)->orderByDesc('updated_at')->first())->updated_at?->format('d M Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
