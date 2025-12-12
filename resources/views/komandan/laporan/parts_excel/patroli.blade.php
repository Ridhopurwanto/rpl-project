<h3>LAPORAN PATROLI</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="7">NO</th>
            <th style="{{ $thStyle }}" width="18">FOTO</th>
            <th style="{{ $thStyle }}" width="15">TANGGAL</th>
            <th style="{{ $thStyle }}" width="30">PETUGAS</th>
            <th style="{{ $thStyle }}" width="12">WAKTU</th>
            <th style="{{ $thStyle }}" width="20">WILAYAH PATROLI</th>
            <th style="{{ $thStyle }}" width="20">JENIS PATROLI</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}; text-align: center; vertical-align: middle;">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto" style="display: block; margin: auto;">
                    @else - @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td style="{{ $tdStyle }}">{{ $item->nama_lengkap ?? '-' }}</td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_exact)->format('H:i') }}</td>
                <td style="{{ $tdStyle }}">{{ $item->wilayah ?? '-' }}</td>
                <td style="{{ $tdCenterStyle }}">{{ $item->jenis_patroli ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="{{ $tdCenterStyle }}">Tidak ada data patroli</td></tr>
        @endforelse
    </tbody>
</table>
