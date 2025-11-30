<h2 style="margin-top: 20px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
    LAPORAN PRESENSI
</h2>

<table style="border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">TANGGAL</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 25%">NAMA ANGGOTA</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU ABSEN</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">STATUS</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">JENIS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($presensi ?? [] as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">
                    {{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ ucfirst($item->status) }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ ucfirst($item->jenis_presensi) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data presensi
                </td>
            </tr>
        @endforelse
    </tbody>
</table>