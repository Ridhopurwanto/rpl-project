<h3>LAPORAN TAMU</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="5">NO</th>
            <th style="{{ $thStyle }}" width="12">TANGGAL</th>
            <th style="{{ $thStyle }}" width="10">WAKTU KUNJUNGAN</th>
            <th style="{{ $thStyle }}" width="20">NAMA TAMU</th>
            <th style="{{ $thStyle }}" width="18">INSTANSI</th>
            <th style="{{ $thStyle }}" width="20">TUJUAN / KEPERLUAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_datang ?? $item->created_at)->format('d/m/Y') }}</td>
                <td style="{{ $tdCenterStyle }}">{{ ($item->waktu_datang ?? null) ? \Carbon\Carbon::parse($item->waktu_datang)->format('H:i') : '-' }}</td>
                <td style="{{ $tdStyle }}">{{ $item->nama_tamu ?? '-' }}</td>
                <td style="{{ $tdStyle }}">{{ $item->instansi ?? '-' }}</td>
                <td style="{{ $tdStyle }}">{{ $item->tujuan ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data tamu</td></tr>
        @endforelse
    </tbody>
</table>
