<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    DAFTAR KENDARAAN TERDAFTAR
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 5%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%">NOMOR PLAT</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 40%">PEMILIK</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 30%">TIPE</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kendaraan_terdaftar as $index => $v)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $v->nomor_plat }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $v->pemilik }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ ucfirst($v->tipe) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 6px; text-align: center;">Tidak ada data kendaraan terdaftar.</td>
            </tr>
        @endforelse
    </tbody>
</table>
