<h3>DAFTAR ANGGOTA</h3>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="{{ $thStyle }}" width="4">NO</th>
            <th style="{{ $thStyle }}" width="7">FOTO</th>
            <th style="{{ $thStyle }}" width="18">NAMA LENGKAP</th>
            <th style="{{ $thStyle }}" width="8">PERAN</th>
            <th style="{{ $thStyle }}" width="8">JADWAL</th>
            <th style="{{ $thStyle }}" width="11">TGL LAHIR</th>
            <th style="{{ $thStyle }}" width="22">ALAMAT</th>
            <th style="{{ $thStyle }}" width="18">EMAIL</th>
            <th style="{{ $thStyle }}" width="11">NO. HP</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $user)
            <tr>
                <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                <td style="{{ $tdCenterStyle }}">
                    @if($user->foto_profil)
                        <img src="{{ public_path('storage/' . $user->foto_profil) }}" height="50" width="auto">
                    @else - @endif
                </td>
                <td style="{{ $tdStyle }}; white-space: nowrap;">{{ $user->nama_lengkap }}</td>
                <td style="{{ $tdCenterStyle }}; white-space: nowrap;">{{ ucfirst($user->peran) }}</td>
                <td style="{{ $tdCenterStyle }}; white-space: nowrap;">{{ ucwords(str_replace('_', ' ', $user->jenis_jadwal ?? '-')) }}</td>
                <td style="{{ $tdCenterStyle }}">
                    {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d-m-Y') : '-' }}
                </td>
                <td style="{{ $tdStyle }}">{{ $user->alamat ?? '-' }}</td>
                <td style="{{ $tdStyle }}; white-space: nowrap;">{{ $user->email }}</td>
                <td style="{{ $tdCenterStyle }}; white-space: nowrap;">{{ $user->no_hp ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="{{ $tdCenterStyle }}">Tidak ada data anggota.</td>
            </tr>
        @endforelse
    </tbody>
</table>
