<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    
    @php
        // --- STYLE SETUP ---
        // Header: Abu-abu, Bold, Center, Border Hitam, Wrap Text
        $thStyle = 'font-size: 10pt; border: 1px solid #000000; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle; white-space: normal; word-wrap: break-word;';        
        
        // Body: Align Top (biar rapi kalo teks panjang), Wrap Text, Border Hitam
        $tdStyle = 'font-size: 10pt; border: 1px solid #000000; vertical-align: top; white-space: normal; word-wrap: break-word;';
        
        // Body Center: Khusus kolom yang isinya pendek/tengah
        $tdCenterStyle = 'font-size: 10pt; border: 1px solid #000000; vertical-align: top; text-align: center; white-space: normal; word-wrap: break-word;';
        
        // Hitung jumlah kolom (colspan) berdasarkan tipe sheet agar Header pas lebarnya
        $colspans = [
            'presensi' => 6,
            'patroli' => 7,
            'barang' => 8,
            'kendaraan' => 7,
            'tamu' => 6,
            'gangguan' => 6,
            'shift' => 3 + \Carbon\Carbon::parse($meta['tanggalMulai'])->diffInDays(\Carbon\Carbon::parse($meta['tanggalSelesai'])) + 1,
            'anggota' => 9, 
            'kendaraan_terdaftar' => 4 
        ];
        $cp = $colspans[$sheetType] ?? 6;
        
        // Logika colspan untuk judul tengah (Total kolom - 2 untuk logo kiri & kanan)
        $centerColspan = max(1, $cp - 2);
    @endphp

    {{-- === HEADER KOP === --}}
    {{-- width="100%" dihapus dari td logo agar tidak maksa kolom jadi lebar --}}
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            {{-- Logo Kiri (Masuk di Kolom 1) --}}
            <td style="text-align: center; vertical-align: middle; height: 100px;">
                <img src="{{ public_path('images/stis-logo.png') }}" height="70" width="auto">
            </td>

            {{-- Judul Tengah (Span ke kolom tengah) --}}
            <td colspan="{{ $centerColspan }}" style="text-align: center; vertical-align: middle;">
                <strong style="font-size: 14pt;">LAPORAN BULANAN KEGIATAN SECURITY</strong><br>
                <span style="font-size: 12pt;">PT. PANCA KHARISMA UTAMA</span><br>
                <span style="font-size: 11pt;">OBYEK PENGAMANAN: POLITEKNIK STATISTIKA STIS JAKARTA</span><br>
                <strong style="font-size: 12pt;">
                    PERIODE: {{ \Carbon\Carbon::parse($meta['tanggalMulai'])->isoFormat('D MMMM Y') }} 
                    S/D 
                    {{ \Carbon\Carbon::parse($meta['tanggalSelesai'])->isoFormat('D MMMM Y') }}
                </strong>
            </td>

            {{-- Logo Kanan (Masuk di Kolom Terakhir) --}}
            <td style="text-align: center; vertical-align: middle; height: 100px;">
                <img src="{{ public_path('images/pku-logo.png') }}" height="70" width="auto">
            </td>
        </tr>
    </table>
    
    {{-- Spacer Row --}}
    <table><tr><td colspan="{{ $cp }}" style="height: 20px;"></td></tr></table>

    {{-- ============================================================ --}}
    {{-- SHEET 1: PRESENSI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'presensi')
        @php
            $presensiMasuk = $data->filter(fn($i) => strtolower($i->jenis_presensi) == 'masuk');
            $presensiPulang = $data->filter(fn($i) => strtolower($i->jenis_presensi) == 'pulang');
        @endphp

        <h3>LAPORAN PRESENSI MASUK</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="10">FOTO</th>
                    <th style="{{ $thStyle }}" width="12">TANGGAL</th>
                    <th style="{{ $thStyle }}" width="33">NAMA ANGGOTA</th>
                    <th style="{{ $thStyle }}" width="10">WAKTU ABSEN</th>
                    <th style="{{ $thStyle }}" width="15">STATUS</th>
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
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="10">FOTO</th>
                    <th style="{{ $thStyle }}" width="12">TANGGAL</th>
                    <th style="{{ $thStyle }}" width="33">NAMA ANGGOTA</th>
                    <th style="{{ $thStyle }}" width="10">WAKTU ABSEN</th>
                    <th style="{{ $thStyle }}" width="15">STATUS</th>
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
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data presensi pulang</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 2: PATROLI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'patroli')
        <h3>LAPORAN PATROLI</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="10">FOTO</th>
                    <th style="{{ $thStyle }}" width="12">TANGGAL</th>
                    <th style="{{ $thStyle }}" width="19">PETUGAS</th>
                    <th style="{{ $thStyle }}" width="8">WAKTU</th>
                    <th style="{{ $thStyle }}" width="19">WILAYAH / LOKASI</th>
                    <th style="{{ $thStyle }}" width="12">JENIS PATROLI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                        <td style="{{ $tdCenterStyle }}">
                            @if(isset($item->foto) && $item->foto)
                                <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                            @else - @endif
                        </td>
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->nama_lengkap ?? '-' }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_exact)->format('H:i') }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->wilayah ?? '-' }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $item->jenis_patroli ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="{{ $tdCenterStyle }}">Tidak ada data patroli</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 3: BARANG --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'barang')
        
        @if(isset($data['temu']) && count($data['temu']) > 0)
            <h3>BARANG TEMUAN</h3>
            <table border="1" style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="{{ $thStyle }}" width="4">NO</th>
                        <th style="{{ $thStyle }}" width="9">FOTO</th>
                        <th style="{{ $thStyle }}" width="12">WAKTU LAPOR</th>
                        <th style="{{ $thStyle }}" width="13">NAMA BARANG</th>
                        <th style="{{ $thStyle }}" width="12">PELAPOR</th>
                        <th style="{{ $thStyle }}" width="12">LOKASI</th>
                        <th style="{{ $thStyle }}" width="8">STATUS</th>
                        <th style="{{ $thStyle }}" width="15">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['temu'] as $index => $item)
                        <tr>
                            <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                            <td style="{{ $tdCenterStyle }}">
                                @if(isset($item->foto) && $item->foto)
                                    <div><strong>Barang:</strong><br><img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto"></div>
                                @endif
                                @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                                    <div><strong>Penerima:</strong><br><img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto"></div>
                                @endif
                            </td>
                            <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->nama_barang ?? '-' }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->nama_pelapor ?? '-' }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->lokasi_penemuan ?? '-' }}</td>
                            <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status ?? '-') }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        @endif

        @if(isset($data['titip']) && count($data['titip']) > 0)
            <h3>BARANG TITIPAN</h3>
            <table border="1" style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="{{ $thStyle }}" width="4">NO</th>
                        <th style="{{ $thStyle }}" width="9">FOTO</th>
                        <th style="{{ $thStyle }}" width="12">WAKTU TITIP</th>
                        <th style="{{ $thStyle }}" width="13">NAMA BARANG</th>
                        <th style="{{ $thStyle }}" width="12">PENITIP</th>
                        <th style="{{ $thStyle }}" width="12">PENERIMA</th>
                        <th style="{{ $thStyle }}" width="8">STATUS</th>
                        <th style="{{ $thStyle }}" width="15">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['titip'] as $index => $item)
                        <tr>
                            <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                            <td style="{{ $tdCenterStyle }}">
                                @if(isset($item->foto) && $item->foto)
                                    <div><strong>Barang:</strong><br><img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto"></div>
                                @endif
                                @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                                    <div><strong>Penerima:</strong><br><img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto"></div>
                                @endif
                            </td>
                            <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_titip ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->nama_barang ?? '-' }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->nama_penitip ?? '-' }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->tujuan ?? '-' }}</td>
                            <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status ?? '-') }}</td>
                            <td style="{{ $tdStyle }}">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if((!isset($data['temu']) || count($data['temu']) == 0) && (!isset($data['titip']) || count($data['titip']) == 0))
             <p>Tidak ada data barang temuan dan titipan</p>
        @endif
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 4: KENDARAAN --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'kendaraan')
        <h3>LAPORAN KENDARAAN</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="12">WAKTU MASUK</th>
                    <th style="{{ $thStyle }}" width="12">WAKTU KELUAR</th>
                    <th style="{{ $thStyle }}" width="13">NOPOL</th>
                    <th style="{{ $thStyle }}" width="18">PEMILIK</th>
                    <th style="{{ $thStyle }}" width="10">TIPE</th>
                    <th style="{{ $thStyle }}" width="15">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}</td>
                        <td style="{{ $tdCenterStyle }}; font-weight: bold; text-transform: uppercase;">{{ $item->nopol ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->pemilik ?? '-' }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $item->tipe ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="{{ $tdCenterStyle }}">Tidak ada data kendaraan</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 5: TAMU --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'tamu')
        <h3>LAPORAN TAMU</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="12">TANGGAL</th>
                    <th style="{{ $thStyle }}" width="10">WAKTU KUNJUNGAN</th>
                    <th style="{{ $thStyle }}" width="20">NAMA TAMU</th>
                    <th style="{{ $thStyle }}" width="18">INSTANSI</th>
                    <th style="{{ $thStyle }}" width="20">TUJUAN / KEPERLUAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_datang ?? $item->created_at)->format('d/m/Y') }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ ($item->waktu_datang ?? null) ? \Carbon\Carbon::parse($item->waktu_datang)->format('H:i') : '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->nama_tamu ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->instansi ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->tujuan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data tamu</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 6: GANGGUAN --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'gangguan')
        <h3>LAPORAN GANGGUAN KAMTIBMAS</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="{{ $thStyle }}" width="5">NO</th>
                    <th style="{{ $thStyle }}" width="10">FOTO</th>
                    <th style="{{ $thStyle }}" width="12">WAKTU LAPOR</th>
                    <th style="{{ $thStyle }}" width="15">KATEGORI</th>
                    <th style="{{ $thStyle }}" width="20">LOKASI</th>
                    <th style="{{ $thStyle }}" width="23">DESKRIPSI KEJADIAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                        <td style="{{ $tdCenterStyle }}">
                            @if(isset($item->foto) && $item->foto)
                                <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                            @else - @endif
                        </td>
                        <td style="{{ $tdCenterStyle }}">{{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $item->kategori ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->lokasi ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $item->deskripsi ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="{{ $tdCenterStyle }}">Tidak ada data gangguan</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 7: SHIFT (MATRIKS) --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'shift')
        @php
            $start = \Carbon\Carbon::parse($meta['tanggalMulai']);
            $end = \Carbon\Carbon::parse($meta['tanggalSelesai']);
            $period = \Carbon\CarbonPeriod::create($start, $end);

            $shiftMatrix = [];
            $userDetails = [];

            foreach($data as $s) {
                $dateKey = \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d');
                $userId = $s->id_pengguna;
                
                if (!isset($userDetails[$userId])) {
                    $namaLengkap = $s->nama_lengkap ?? $s->user->nama_lengkap ?? $s->pengguna->nama_lengkap ?? '-';
                    $peran = $s->peran ?? $s->user->peran ?? $s->pengguna->peran ?? '-';

                    $userDetails[$userId] = [
                        'name' => $namaLengkap,
                        'role' => ucfirst($peran) 
                    ];
                }

                $namaShift = $s->shiftRule->jenis_shift ?? $s->shift_rule->jenis_shift ?? 'Off'; 
                $kode = strtoupper(substr($namaShift, 0, 1));
                
                $shiftMatrix[$userId][$dateKey] = $kode;
            }
        @endphp

        <div style="margin-bottom: 10px; font-size: 10pt;">
            <strong>KETERANGAN:</strong> 
            <span style="background-color: #ffff00; padding: 2px 5px; border: 1px solid #000;">P: PAGI</span>
            <span style="background-color: #4fc3f7; padding: 2px 5px; border: 1px solid #000;">M: MALAM</span>
            <span style="background-color: #ff5252; color: white; padding: 2px 5px; border: 1px solid #000;">O: OFF/LIBUR</span>
            <span style="background-color: #e0e0e0; padding: 2px 5px; border: 1px solid #000;">N: NON SHIFT</span>
        </div>

        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th rowspan="2" style="{{ $thStyle }}" width="5">NO</th>
                    <th rowspan="2" style="{{ $thStyle }}" width="25">NAMA PERSONIL</th>
                    <th rowspan="2" style="{{ $thStyle }}" width="15">JABATAN</th> 
                    @foreach($period as $date)
                        <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 4;" width="4">{{ $date->format('d') }}</th>
                    @endforeach
                </tr>
                <tr>
                     @foreach($period as $date)
                        <th style="border: 1px solid #000000; text-align: center; font-size: 8pt; height: 15px;">
                            {{ strtoupper(substr($date->isoFormat('dddd'), 0, 3)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($userDetails as $userId => $info)
                    <tr>
                        <td style="{{ $tdCenterStyle }}">{{ $no++ }}</td>
                        <td style="{{ $tdStyle }}">{{ $info['name'] }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $info['role'] }}</td> 
                        
                        @foreach($period as $date)
                            @php
                                $dKey = $date->format('Y-m-d');
                                $kode = $shiftMatrix[$userId][$dKey] ?? '';
                                
                                $baseStyle = "text-align: center; border: 1px solid #000000; font-weight: bold;";
                                if($kode == 'P') $style = $baseStyle . "background-color: #ffff00; color: #000000;";      
                                elseif($kode == 'M') $style = $baseStyle . "background-color: #4fc3f7; color: #000000;";  
                                elseif($kode == 'O') $style = $baseStyle . "background-color: #ff5252; color: #ffffff;";  
                                elseif($kode == 'N') $style = $baseStyle . "background-color: #e0e0e0; color: #000000;"; 
                                else $style = $baseStyle; 
                            @endphp

                            <td style="{{ $style }}">{{ $kode }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 8: ANGGOTA --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'anggota')
        <h3>DAFTAR ANGGOTA</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
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
                        <td style="{{ $tdStyle }}">{{ $user->nama_lengkap }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ ucfirst($user->peran) }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $user->jenis_jadwal ?? '-' }}</td>
                        <td style="{{ $tdCenterStyle }}">
                            {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d-m-Y') : '-' }}
                        </td>
                        <td style="{{ $tdStyle }}">{{ $user->alamat ?? '-' }}</td>
                        <td style="{{ $tdStyle }}">{{ $user->email }}</td>
                        <td style="{{ $tdCenterStyle }}">{{ $user->no_hp ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="{{ $tdCenterStyle }}">Tidak ada data anggota.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 9: KENDARAAN TERDAFTAR --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'kendaraan_terdaftar')
        <h3>DAFTAR KENDARAAN TERDAFTAR</h3>
        <table border="1" style="border-collapse: collapse; width: 100%;">
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
    @endif

</body>
</html>