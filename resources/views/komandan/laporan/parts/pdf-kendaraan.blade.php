<h2 style="margin-top: 20px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
    LAPORAN KENDARAAN
</h2>

<table style="border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 9pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 3%">NO</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 11%">WAKTU MASUK</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 11%">WAKTU KELUAR</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 10%">NOPOL</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: left; width: 14%">PEMILIK</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 11%">TIPE</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 11%">STATUS</th>
            <th style="border: 1px solid #000; padding: 5px; text-align: left; width: 14%">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kendaraan ?? [] as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                    {{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; text-transform: uppercase;">
                    {{ $item->nopol ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->pemilik ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $item->tipe ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ ucfirst($item->status ?? '-') }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data kendaraan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>