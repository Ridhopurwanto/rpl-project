{{-- PRESENSI MASUK --}}
<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    LAPORAN PRESENSI MASUK
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px 10px; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">FOTO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">TANGGAL</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 30%">NAMA ANGGOTA</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU ABSEN</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $presensiMasuk = collect($presensi ?? [])->filter(function($item) {
                return strtolower($item->jenis_presensi ?? '') === 'masuk';
            });
        @endphp
        
        @forelse($presensiMasuk as $index => $item)
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
                    {{ $item->nama_lengkap ?? optional($item->pengguna)->nama_lengkap ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ ucfirst($item->status) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data presensi masuk
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- PRESENSI PULANG --}}
<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    LAPORAN PRESENSI PULANG
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px 10px; font-size: 10pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">FOTO</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">TANGGAL</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 30%">NAMA ANGGOTA</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">WAKTU ABSEN</th>
            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $presensiPulang = collect($presensi ?? [])->filter(function($item) {
                return strtolower($item->jenis_presensi ?? '') === 'pulang';
            });
        @endphp
        
        @forelse($presensiPulang as $index => $item)
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
                    {{ $item->nama_lengkap ?? optional($item->pengguna)->nama_lengkap ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ ucfirst($item->status) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; font-style: italic; color: #666;">
                    Tidak ada data presensi pulang
                </td>
            </tr>
        @endforelse
    </tbody>
</table>