<h2 style="margin-top: 20px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
    LAPORAN TAMU
</h2>

<table style="border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU DATANG</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU PULANG</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 22%">NAMA TAMU</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 20%">INSTANSI</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 22%">TUJUAN / KEPERLUAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tamu ?? [] as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu_datang ?? $item->created_at)->format('d/m/Y H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ ($item->waktu_pulang ?? null) ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') : '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_tamu ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $item->instansi ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $item->tujuan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data tamu
                </td>
            </tr>
        @endforelse
    </tbody>
</table>