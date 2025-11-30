<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th { background-color: #cccccc; font-weight: bold; border: 1px solid #000000; text-align: center; height: 30px; vertical-align: middle; font-size: 10pt; }
        td { border: 1px solid #000000; padding: 5px; vertical-align: middle; font-size: 10pt; }
        h2 { margin-top: 20px; color: #2a4a6f; font-size: 14pt; margin-bottom: 10px; }
        .text-center { text-align: center; }
        .empty { text-align: center; font-style: italic; color: #666; padding: 10px; }
        
        /* CSS Shift */
        .shift-cell { text-align: center; width: 30px; font-weight: bold; border: 1px solid #000; }
        .shift-P { background-color: #ffff00; color: #000000; } 
        .shift-M { background-color: #4fc3f7; color: #000000; } 
        .shift-O { background-color: #ff5252; color: #ffffff; } 
        .shift-N { background-color: #e0e0e0; color: #000000; } 
    </style>
</head>
<body>
    
    {{-- HEADER (Muncul di setiap sheet) --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 18pt; font-weight: bold; margin: 0;">
            {{ strtoupper('Laporan ' . str_replace('_', ' ', $sheetType)) }}
        </h1>
        <p style="margin-top: 5px;">PERIODE: 
            <strong>{{ \Carbon\Carbon::parse($meta['tanggalMulai'])->isoFormat('D MMMM Y') }}</strong> 
            S/D 
            <strong>{{ \Carbon\Carbon::parse($meta['tanggalSelesai'])->isoFormat('D MMMM Y') }}</strong>
        </p>
    </div>
    <hr>

    {{-- ============================================================ --}}
    {{-- SHEET 1: PRESENSI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'presensi')
        <table>
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">TANGGAL</th>
                    <th width="25">NAMA ANGGOTA</th>
                    <th width="15">WAKTU ABSEN</th>
                    <th width="15">STATUS</th>
                    <th width="15">JENIS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap ?? $item->user->nama_lengkap ?? '-' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                        <td class="text-center">{{ ucfirst($item->jenis_presensi) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data presensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ============================================================ --}}
    {{-- SHEET 2: PATROLI --}}
    {{-- ============================================================ --}}
    @if($sheetType == 'patroli')
        <table>
            <thead>
                <tr>
                    <th width="5">NO</th>
                    <th width="15">TANGGAL</th>
                    <th width="25">PETUGAS</th>
                    <th width="15">WAKTU TEPAT</th>
                    <th width="30">WILAYAH / LOKASI</th>
                    <th width="20">JENIS PATROLI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu_exact)->format('H:i') }}</td>
                        <td>{{ $item->wilayah }}</td>
                        <td class="text-center">{{ $item->jenis_patroli }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data patroli.</td></tr>
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
            <table>
                <thead>
                    <tr>
                        <th width="5">NO</th>
                        <th width="15">WAKTU LAPOR</th>
                        <th width="25">NAMA BARANG</th>
                        <th width="25">PELAPOR</th>
                        <th width="25">LOKASI PENEMUAN</th>
                        <th width="15">STATUS</th>
                        <th width="25">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['temu'] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
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
            <table>
                <thead>
                    <tr>
                        <th width="5">NO</th>
                        <th width="15">WAKTU TITIP</th>
                        <th width="25">NAMA BARANG</th>
                        <th width="25">PENITIP</th>
                        <th width="25">TUJUAN</th>
                        <th width="15">STATUS</th>
                        <th width="25">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['titip'] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
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
        <table>
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
        <table>
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
        <table>
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

        <table>
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