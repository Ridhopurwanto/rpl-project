<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th { background-color: #cccccc; font-weight: bold; border: 1px solid #000000 !important; text-align: center; height: 30px; vertical-align: middle; font-size: 10pt; }
        td { border: 1px solid #000000 !important; padding: 5px; vertical-align: middle; font-size: 10pt; word-wrap: break-word; white-space: normal; }
        h2, h3 { margin-top: 20px; color: #2a4a6f; font-size: 14pt; margin-bottom: 10px; }
        .text-center { text-align: center; vertical-align: middle; }
        .empty { text-align: center; font-style: italic; color: #666; padding: 10px; vertical-align: middle; border: 1px solid #000000 !important; }
        
        /* CSS Shift */
        .shift-cell { text-align: center; width: 30px; font-weight: bold; border: 1px solid #000000 !important; vertical-align: middle; }
        .shift-P { background-color: #ffff00; color: #000000; } 
        .shift-M { background-color: #4fc3f7; color: #000000; } 
        .shift-O { background-color: #ff5252; color: #ffffff; } 
        .shift-N { background-color: #e0e0e0; color: #000000; } 
    </style>
</head>
<body>
    
    @php
        $colspans = [
            'presensi' => 6,
            'patroli' => 7,
            'barang' => 8,
            'kendaraan' => 8,
            'tamu' => 6,
            'gangguan' => 5,
            'shift' => 3 + \Carbon\Carbon::parse($meta['tanggalMulai'])->diffInDays(\Carbon\Carbon::parse($meta['tanggalSelesai'])) + 1
        ];
        $cp = $colspans[$sheetType] ?? 6;
        
        $centerColspan = max(1, $cp - 2);
    @endphp

    {{-- HEADER --}}
    <table style="margin-bottom: 20px; border: none; width: 100%;">
        <tr>
            {{-- LOGO KIRI --}}
            <td style="text-align: center; vertical-align: middle; border: none; height: 100px;">
                <img src="{{ public_path('images/stis-logo.png') }}" height="70" width="auto">
            </td>

            {{-- TEXT TENGAH --}}
            <td colspan="{{ $centerColspan }}" style="text-align: center; vertical-align: middle; border: none;">
                <strong style="font-size: 14pt;">LAPORAN BULANAN KEGIATAN SECURITY</strong><br>
                <span style="font-size: 12pt;">PT. PANCA KHARISMA UTAMA</span><br>
                <span style="font-size: 11pt;">OBYEK PENGAMANAN: POLITEKNIK STATISTIKA STIS JAKARTA</span><br>
                <strong style="font-size: 12pt;">
                    PERIODE: {{ \Carbon\Carbon::parse($meta['tanggalMulai'])->isoFormat('D MMMM Y') }} 
                    S/D 
                    {{ \Carbon\Carbon::parse($meta['tanggalSelesai'])->isoFormat('D MMMM Y') }}
                </strong>
            </td>

            {{-- LOGO KANAN --}}
            <td style="text-align: center; vertical-align: middle; border: none; height: 100px;">
                <img src="{{ public_path('images/pku-logo.png') }}" height="70" width="auto">
            </td>
        </tr>
    </table>
    
    {{-- Spacer Row --}}
    <table><tr><td colspan="{{ $cp }}" style="border: none; height: 20px;"></td></tr></table>

    {{-- ============================================================ --}}
    {{-- SHEET 1: PRESENSI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'presensi')
        @php
            $presensiMasuk = $data->filter(function($item) {
                return strtolower($item->jenis_presensi) == 'masuk';
            });
            $presensiPulang = $data->filter(function($item) {
                return strtolower($item->jenis_presensi) == 'pulang';
            });
        @endphp

        {{-- TABEL MASUK --}}
        <h3>LAPORAN PRESENSI MASUK</h3>
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">FOTO</th>
                    <th width="15">TANGGAL</th>
                    <th width="25">NAMA ANGGOTA</th>
                    <th width="15">WAKTU ABSEN</th>
                    <th width="15">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensiMasuk as $index => $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center" height="60">
                            @if(isset($item->foto) && $item->foto)
                                <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data presensi masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
        
        <br>

        {{-- TABEL PULANG --}}
        <h3>LAPORAN PRESENSI PULANG</h3>
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">FOTO</th>
                    <th width="15">TANGGAL</th>
                    <th width="25">NAMA ANGGOTA</th>
                    <th width="15">WAKTU ABSEN</th>
                    <th width="15">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensiPulang as $index => $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center" height="60">
                            @if(isset($item->foto) && $item->foto)
                                <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data presensi pulang.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 2: PATROLI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'patroli')
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">FOTO</th>
                    <th width="15">TANGGAL</th>
                    <th width="20">PETUGAS</th>
                    <th width="10">WAKTU</th>
                    <th width="25">WILAYAH / LOKASI</th>
                    <th width="15">JENIS PATROLI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center" height="60">
                            @if(isset($item->foto) && $item->foto)
                                <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu_exact)->format('H:i') }}</td>
                        <td>{{ $item->wilayah }}</td>
                        <td class="text-center">{{ $item->jenis_patroli }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada data patroli.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 3: BARANG (GABUNGAN TEMU & TITIP DALAM SATU SHEET) --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'barang')
        
        {{-- Bagian A: Barang Temuan (Jika ada datanya) --}}
        @if(isset($data['temu']) && count($data['temu']) > 0)
            <h2>BARANG TEMUAN</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th width="5">NO</th>
                        <th width="15">FOTO</th>
                        <th width="15">WAKTU LAPOR</th>
                        <th width="20">NAMA BARANG</th>
                        <th width="15">PELAPOR</th>
                        <th width="15">LOKASI</th>
                        <th width="10">STATUS</th>
                        <th width="20">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['temu'] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center" height="60">
                                @if(isset($item->foto) && $item->foto)
                                    <div style="margin-bottom: 5px;">
                                        <strong>Barang:</strong><br>
                                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                                    </div>
                                @endif
                                @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                                    <div style="margin-top: 5px;">
                                        <strong>Penerima:</strong><br>
                                        <img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto">
                                    </div>
                                @endif
                                @if((!isset($item->foto) || !$item->foto) && (!isset($item->foto_penerima) || !$item->foto_penerima))
                                    -
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->waktu_lapor)->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_pelapor }}</td>
                            <td>{{ $item->lokasi_penemuan }}</td>
                            <td class="text-center">{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        @endif

        {{-- Bagian B: Barang Titipan (Jika ada datanya) --}}
        @if(isset($data['titip']) && count($data['titip']) > 0)
            <h2>BARANG TITIPAN</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th width="5">NO</th>
                        <th width="15">FOTO</th>
                        <th width="15">WAKTU TITIP</th>
                        <th width="20">NAMA BARANG</th>
                        <th width="15">PENITIP</th>
                        <th width="15">PENERIMA</th>
                        <th width="10">STATUS</th>
                        <th width="20">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['titip'] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center" height="60">
                                @if(isset($item->foto) && $item->foto)
                                    <div style="margin-bottom: 5px;">
                                        <strong>Barang:</strong><br>
                                        <img src="{{ public_path('storage/' . $item->foto) }}" height="50" width="auto">
                                    </div>
                                @endif
                                @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                                    <div style="margin-top: 5px;">
                                        <strong>Penerima:</strong><br>
                                        <img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto">
                                    </div>
                                @endif
                                @if((!isset($item->foto) || !$item->foto) && (!isset($item->foto_penerima) || !$item->foto_penerima))
                                    -
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->waktu_titip)->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_penitip }}</td>
                            <td>{{ $item->tujuan }}</td>
                            <td class="text-center">{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if((!isset($data['temu']) || count($data['temu']) == 0) && (!isset($data['titip']) || count($data['titip']) == 0))
             <p class="empty">Tidak ada data barang.</p>
        @endif
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 4: KENDARAAN --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'kendaraan')
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">WAKTU MASUK</th>
                    <th width="15">WAKTU KELUAR</th>
                    <th width="15">NOPOL</th>
                    <th width="20">PEMILIK</th>
                    <th width="15">TIPE</th>
                    <th width="15">STATUS</th>
                    <th width="20">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}</td>
                        <td style="text-transform: uppercase; font-weight: bold;">{{ $item->nopol }}</td>
                        <td>{{ $item->pemilik }}</td>
                        <td class="text-center">{{ $item->tipe }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Tidak ada data log kendaraan.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 5: TAMU --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'tamu')
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">WAKTU DATANG</th>
                    <th width="15">WAKTU PULANG</th>
                    <th width="25">NAMA TAMU</th>
                    <th width="20">INSTANSI</th>
                    <th width="25">TUJUAN / KEPERLUAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_datang)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') : '-' }}</td>
                        <td>{{ $item->nama_tamu }}</td>
                        <td>{{ $item->instansi }}</td>
                        <td>{{ $item->tujuan }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data tamu.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 6: GANGGUAN --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'gangguan')
        <table border="1">
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">WAKTU LAPOR</th>
                    <th width="20">KATEGORI</th>
                    <th width="25">LOKASI</th>
                    <th width="40">DESKRIPSI KEJADIAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_lapor)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->kategori }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td>{{ $item->deskripsi }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Tidak ada data gangguan.</td></tr>
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
                
                // Ambil info user jika belum ada
                if (!isset($userDetails[$userId])) {
                    // LOGIKA BARU: Cek berbagai kemungkinan nama properti/relasi
                    // 1. Cek $s->nama_lengkap (jika sudah di-join query builder)
                    // 2. Cek $s->user->nama_lengkap (relasi standard 'user')
                    // 3. Cek $s->pengguna->nama_lengkap (relasi nama tabel 'pengguna')
                    $namaLengkap = $s->nama_lengkap ?? $s->user->nama_lengkap ?? $s->pengguna->nama_lengkap ?? '-';
                    
                    // Sama untuk peran/jabatan
                    $peran = $s->peran ?? $s->user->peran ?? $s->pengguna->peran ?? '-';

                    $userDetails[$userId] = [
                        'name' => $namaLengkap,
                        'role' => ucfirst($peran) 
                    ];
                }

                // Ambil jenis shift dari relasi
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

        <table border="1">
            <thead>
                <tr>
                    <th width="5" rowspan="2">NO</th>
                    <th width="25" rowspan="2">NAMA PERSONIL</th>
                    <th width="15" rowspan="2">JABATAN</th> 
                    @foreach($period as $date)
                        <th style="text-align: center; width: 30px; font-weight: bold; border: 1px solid #000;">{{ $date->format('d') }}</th>
                    @endforeach
                </tr>
                <tr>
                     @foreach($period as $date)
                        <th style="font-size: 8pt; height: 15px; text-align: center; border: 1px solid #000;">
                            {{ strtoupper(substr($date->isoFormat('dddd'), 0, 3)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($userDetails as $userId => $info)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $info['name'] }}</td>
                        <td class="text-center">{{ $info['role'] }}</td> 
                        
                        @foreach($period as $date)
                            @php
                                $dKey = $date->format('Y-m-d');
                                $kode = $shiftMatrix[$userId][$dKey] ?? '';
                                
                                $style = "text-align: center; border: 1px solid #000; font-weight: bold;";
                                if($kode == 'P') $style .= "background-color: #ffff00; color: #000000;";      
                                elseif($kode == 'M') $style .= "background-color: #4fc3f7; color: #000000;";  
                                elseif($kode == 'O') $style .= "background-color: #ff5252; color: #ffffff;";  
                                elseif($kode == 'N') $style .= "background-color: #e0e0e0; color: #000000;";  
                            @endphp

                            <td style="{{ $style }}">
                                {{ $kode }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>