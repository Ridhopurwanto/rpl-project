<h3>LAPORAN KENDARAAN</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="5">NO</th>
            <th style="{{ $thStyle }}" width="12">WAKTU MASUK</th>
            <th style="{{ $thStyle }}" width="12">WAKTU KELUAR</th>
            <th style="{{ $thStyle }}" width="13">NOPOL</th>
            <th style="{{ $thStyle }}" width="18">PEMILIK</th>
            <th style="{{ $thStyle }}" width="10">TIPE</th>
            <th style="{{ $thStyle }}" width="15">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') }}</td>
                <td style="{{ $tdCenterStyle }}">{{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}</td>
                <td style="{{ $tdCenterStyle }}; text-transform: uppercase;">
                    {{ $item->nopol ?? $item->kendaraan->nopol ?? '-' }}
                </td>
                <td style="{{ $tdStyle }}">
                    {{ $item->pemilik ?? $item->kendaraan->pemilik ?? '-' }}
                </td>
                <td style="{{ $tdCenterStyle }}">
                    {{ $item->tipe ?? $item->kendaraan->tipe ?? '-' }}
                </td>
                <td style="{{ $tdStyle }}">{{ $item->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="{{ $tdCenterStyle }}">Tidak ada data kendaraan</td></tr>
        @endforelse
    </tbody>
</table>
