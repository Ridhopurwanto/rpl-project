{{-- Ini adalah template Excel. Gunakan tag <table> sederhana. --}}
<html>
<body>
    <h1>Laporan Gabungan</h1>
    <p>Periode: 
        <strong>{{ \Carbon\Carbon::parse($dataGabungan['tanggalMulai'])->format('d M Y') }}</strong> 
        s/d 
        <strong>{{ \Carbon\Carbon::parse($dataGabungan['tanggalSelesai'])->format('d M Y') }}</strong>
    </p>
    <hr>

    {{-- ============================================= --}}
    {{-- CEK APAKAH LAPORAN PRESENSI DIMINTA --}}
    {{-- ============================================= --}}
    @if(isset($dataGabungan['presensi']))
        <h2>Laporan Presensi</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Jenis</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['presensi'] as $item)
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->waktu }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->jenis_presensi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data presensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================= --}}
    {{-- CEK APAKAH LAPORAN PATROLI DIMINTA --}}
    {{-- ============================================= --}}
    @if(isset($dataGabungan['patroli']))
        <h2>Laporan Patroli</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Waktu</th>
                    <th>Wilayah</th>
                    <th>Jenis Patroli</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['patroli'] as $item)
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->waktu_exact }}</td>
                        <td>{{ $item->wilayah }}</td>
                        <td>{{ $item->jenis_patroli }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data patroli.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
    
    {{-- (Anda bisa tambahkan @if(isset($dataGabungan['tamu'])) ... dst. di sini) --}}

</body>
</html>