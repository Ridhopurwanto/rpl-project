<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    DAFTAR ANGGOTA
</h2>

<table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 9pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 4%">NO</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 8%">FOTO</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: left; width: 15%">NAMA LENGKAP</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 8%">PERAN</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 8%">JADWAL</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 10%">TGL LAHIR</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: left; width: 17%">ALAMAT</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: left; width: 15%">EMAIL</th>
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 10%">NO. HP</th>
        </tr>
    </thead>
    <tbody>
        @forelse($anggota as $index => $user)
            <tr>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">
                    @if($user->foto_profil)
                        <img src="{{ public_path('storage/' . $user->foto_profil) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $user->nama_lengkap }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ ucfirst($user->peran) }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $user->jenis_jadwal ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">
                    {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d-m-Y') : '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $user->alamat ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 4px; word-wrap: break-word;">{{ $user->email }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $user->no_hp ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="border: 1px solid #000; padding: 6px; text-align: center;">Tidak ada data anggota.</td>
            </tr>
        @endforelse
    </tbody>
</table>
