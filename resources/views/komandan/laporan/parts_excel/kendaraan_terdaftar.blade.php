<h3>DAFTAR KENDARAAN TERDAFTAR</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="5">NO</th>
            <th style="{{ $thStyle }}" width="20">NOMOR PLAT</th>
            <th style="{{ $thStyle }}" width="35">PEMILIK</th>
            <th style="{{ $thStyle }}" width="15">TIPE</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $v)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}">{{ $v->nomor_plat }}</td>
                <td style="{{ $tdStyle }}">{{ $v->pemilik }}</td>
                <td style="{{ $tdCenterStyle }}">{{ ucfirst($v->tipe) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="{{ $tdCenterStyle }}">Tidak ada data kendaraan terdaftar.</td>
            </tr>
        @endforelse
    </tbody>
</table>
