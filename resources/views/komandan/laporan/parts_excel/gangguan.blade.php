<h3>LAPORAN GANGGUAN KAMTIBMAS</h3>
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="5">NO</th>
            <th style="{{ $thStyle }}" width="10">FOTO</th>
            <th style="{{ $thStyle }}" width="12">WAKTU LAPOR</th>
            <th style="{{ $thStyle }}" width="15">KATEGORI</th>
            <th style="{{ $thStyle }}" width="20">LOKASI</th>
            <th style="{{ $thStyle }}" width="23">DESKRIPSI KEJADIAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                    @else - @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                <td style="{{ $tdCenterStyle }}">{{ $item->kategori ?? '-' }}</td>
                <td style="{{ $tdStyle }}">{{ $item->lokasi ?? '-' }}</td>
                <td style="{{ $tdStyle }}">{{ $item->deskripsi ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data gangguan</td></tr>
        @endforelse
    </tbody>
</table>
