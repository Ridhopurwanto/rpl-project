<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    LAPORAN PATROLI
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">FOTO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">TANGGAL</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">PETUGAS</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">WAKTU</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 24%">WILAYAH / LOKASI</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">JENIS PATROLI</th>
        </tr>
    </thead>
    <tbody>
        @forelse($patroli ?? [] as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">
                    {{ $item->nama_lengkap ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu_exact)->format('H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">
                    {{ $item->wilayah ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ $item->jenis_patroli ?? '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data patroli
                </td>
            </tr>
        @endforelse
    </tbody>
</table>