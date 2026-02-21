@php
    $presensiMasuk = $data->filter(fn($i) => strtolower($i->jenis_presensi) == 'masuk');
    $presensiPulang = $data->filter(fn($i) => strtolower($i->jenis_presensi) == 'pulang');
@endphp

<h3>LAPORAN PRESENSI MASUK</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="8">NO</th>
            <th style="{{ $thStyle }}" width="20">FOTO</th>
            <th style="{{ $thStyle }}" width="20">TANGGAL</th>
            <th style="{{ $thStyle }}" width="30">NAMA ANGGOTA</th>
            <th style="{{ $thStyle }}" width="10">JENIS SHIFT</th>
            <th style="{{ $thStyle }}" width="20">WAKTU ABSEN</th>
            <th style="{{ $thStyle }}" width="20">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($presensiMasuk as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $loop->iteration }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                    @else - @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td style="{{ $tdStyle }}">{{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if($item->jenis_shift == 1 || $item->jenis_shift == 4)
                        Pagi
                    @elseif($item->jenis_shift == 2)
                        Malam
                    @else
                        {{ $item->jenis_shift }}
                    @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data presensi masuk</td></tr>
        @endforelse
    </tbody>
</table>

<br>

<h3>LAPORAN PRESENSI PULANG</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="8">NO</th>
            <th style="{{ $thStyle }}" width="20">FOTO</th>
            <th style="{{ $thStyle }}" width="20">TANGGAL</th>
            <th style="{{ $thStyle }}" width="30">NAMA ANGGOTA</th>
            <th style="{{ $thStyle }}" width="10">JENIS SHIFT</th>
            <th style="{{ $thStyle }}" width="20">WAKTU ABSEN</th>
            <th style="{{ $thStyle }}" width="20">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($presensiPulang as $index => $item)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $loop->iteration }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if(isset($item->foto) && $item->foto)
                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                    @else - @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td style="{{ $tdStyle }}">{{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if($item->jenis_shift == 1 || $item->jenis_shift == 4)
                        Pagi
                    @elseif($item->jenis_shift == 2)
                        Malam
                    @else
                        {{ $item->jenis_shift }}
                    @endif
                </td>
                <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data presensi pulang</td></tr>
        @endforelse
    </tbody>
</table>
