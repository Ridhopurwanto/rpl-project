<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
    LAPORAN GANGGUAN KAMTIBMAS
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px 10px; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">FOTO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU LAPOR</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">KATEGORI</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 20%">LOKASI</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 32%">DESKRIPSI KEJADIAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($gangguan ?? [] as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" style="max-width: 80px; max-height: 60px; object-fit: cover;">
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $item->kategori ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $item->lokasi ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $item->deskripsi ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data gangguan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
