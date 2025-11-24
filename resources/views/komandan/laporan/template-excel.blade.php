{{-- 
    TEMPLATE EXCEL GABUNGAN (SESUAI DB rpl-projek.sql)
    Update: Pemisahan Barang Temuan & Titipan + Uppercase Header
--}}
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th { background-color: #cccccc; font-weight: bold; border: 1px solid #000000; text-align: center; height: 30px; vertical-align: middle; font-size: 10pt; }
        td { border: 1px solid #000000; padding: 5px; vertical-align: middle; font-size: 10pt; }
        h2 { margin-top: 20px; color: #2a4a6f; font-size: 14pt; }
        .text-center { text-align: center; }
        .empty { text-align: center; font-style: italic; color: #666; padding: 10px; }
    </style>
</head>
<body>
    
    {{-- HEADER --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 18pt; font-weight: bold; margin: 0;">{{ strtoupper('Laporan Gabungan Operasional') }}</h1>
        <p style="margin-top: 5px;">PERIODE: 
            <strong>{{ \Carbon\Carbon::parse($dataGabungan['tanggalMulai'])->isoFormat('D MMMM Y') }}</strong> 
            S/D 
            <strong>{{ \Carbon\Carbon::parse($dataGabungan['tanggalSelesai'])->isoFormat('D MMMM Y') }}</strong>
        </p>
    </div>
    <hr>

    {{-- 1. LAPORAN PRESENSI --}}
    @if(isset($dataGabungan['presensi']))
        <h2>1. {{ strtoupper('Laporan Presensi Anggota') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Tanggal') }}</th>
                    <th width="25">{{ strtoupper('Nama Anggota') }}</th>
                    <th width="15">{{ strtoupper('Waktu Absen') }}</th>
                    <th width="15">{{ strtoupper('Status') }}</th>
                    <th width="15">{{ strtoupper('Jenis') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['presensi'] as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_lengkap ?? $item->pengguna->nama_lengkap ?? '-' }}</td>
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

    {{-- 2. LAPORAN PATROLI --}}
    @if(isset($dataGabungan['patroli']))
        <h2>2. {{ strtoupper('Laporan Patroli Area') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Tanggal') }}</th>
                    <th width="25">{{ strtoupper('Petugas') }}</th>
                    <th width="15">{{ strtoupper('Waktu Tepat') }}</th>
                    <th width="30">{{ strtoupper('Wilayah / Lokasi') }}</th>
                    <th width="20">{{ strtoupper('Jenis Patroli') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['patroli'] as $index => $item)
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

    {{-- 
        3. LAPORAN BARANG TEMUAN (DIPISAH)
    --}}
    @if(isset($dataGabungan['barang_temu']))
        <h2>3. {{ strtoupper('Laporan Barang Temuan') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Waktu Lapor') }}</th>
                    <th width="25">{{ strtoupper('Nama Barang') }}</th>
                    <th width="25">{{ strtoupper('Pelapor') }}</th>
                    <th width="25">{{ strtoupper('Lokasi Penemuan') }}</th>
                    <th width="15">{{ strtoupper('Status') }}</th>
                    <th width="25">{{ strtoupper('Catatan') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['barang_temu'] as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_lapor)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nama_pelapor }}</td>
                        <td>{{ $item->lokasi_penemuan }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada data barang temuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- 
        4. LAPORAN BARANG TITIPAN (DIPISAH)
    --}}
    @if(isset($dataGabungan['barang_titip']))
        <h2>4. {{ strtoupper('Laporan Barang Titipan') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Waktu Titip') }}</th>
                    <th width="25">{{ strtoupper('Nama Barang') }}</th>
                    <th width="25">{{ strtoupper('Nama Penitip') }}</th>
                    <th width="25">{{ strtoupper('Tujuan') }}</th>
                    <th width="15">{{ strtoupper('Status') }}</th>
                    <th width="25">{{ strtoupper('Catatan') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['barang_titip'] as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_titip)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nama_penitip }}</td>
                        <td>{{ $item->tujuan }}</td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada data barang titipan.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- 5. LAPORAN KENDARAAN --}}
    @if(isset($dataGabungan['kendaraan']))
        <h2>5. {{ strtoupper('Laporan Log Kendaraan') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Waktu Masuk') }}</th>
                    <th width="15">{{ strtoupper('Waktu Keluar') }}</th>
                    <th width="15">{{ strtoupper('Nopol') }}</th>
                    <th width="20">{{ strtoupper('Pemilik') }}</th>
                    <th width="15">{{ strtoupper('Tipe') }}</th>
                    <th width="15">{{ strtoupper('Status') }}</th>
                    <th width="20">{{ strtoupper('Keterangan') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['kendaraan'] as $index => $item)
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

    {{-- 6. LAPORAN TAMU --}}
    @if(isset($dataGabungan['tamu']))
        <h2>6. {{ strtoupper('Laporan Buku Tamu') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Waktu Datang') }}</th>
                    <th width="15">{{ strtoupper('Waktu Pulang') }}</th>
                    <th width="25">{{ strtoupper('Nama Tamu') }}</th>
                    <th width="20">{{ strtoupper('Instansi') }}</th>
                    <th width="25">{{ strtoupper('Tujuan / Keperluan') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['tamu'] as $index => $item)
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

    {{-- 7. LAPORAN GANGGUAN --}}
    @if(isset($dataGabungan['gangguan']))
        <h2>7. {{ strtoupper('Laporan Gangguan Kamtibmas') }}</h2>
        <table>
            <thead>
                <tr>
                    <th width="5">{{ strtoupper('No') }}</th>
                    <th width="15">{{ strtoupper('Waktu Lapor') }}</th>
                    <th width="20">{{ strtoupper('Kategori') }}</th>
                    <th width="25">{{ strtoupper('Lokasi') }}</th>
                    <th width="40">{{ strtoupper('Deskripsi Kejadian') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGabungan['gangguan'] as $index => $item)
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

    {{-- 8. LAPORAN SHIFT (MATRIKS) --}}
    @if(isset($dataGabungan['shift']) && count($dataGabungan['shift']) > 0)
        @php
            $start = \Carbon\Carbon::parse($dataGabungan['tanggalMulai']);
            $end = \Carbon\Carbon::parse($dataGabungan['tanggalSelesai']);
            $period = \Carbon\CarbonPeriod::create($start, $end);

            $shiftMatrix = [];
            $userDetails = [];

            foreach($dataGabungan['shift'] as $s) {
                $dateKey = \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d');
                $userId = $s->id_pengguna;
                
                if (!isset($userDetails[$userId])) {
                    $namaLengkap = $s->pengguna->nama_lengkap ?? '-';
                    $peran = $s->pengguna->peran ?? '-';

                    $userDetails[$userId] = [
                        'name' => $namaLengkap,
                        'role' => ucfirst($peran) 
                    ];
                }

                $kode = substr($s->jenis_shift, 0, 1); 
                if ($s->jenis_shift == 'Non Shift') $kode = 'N';
                
                $shiftMatrix[$userId][$dateKey] = $kode;
            }
        @endphp

        <h2>8. {{ strtoupper('Plotting Personil Keamanan (Shift)') }}</h2>
        
        <div style="margin-bottom: 10px; font-size: 10pt;">
            <strong>Keterangan:</strong> 
            <span style="background-color: #ffff00; padding: 2px 5px; border: 1px solid #000;">P: PAGI</span>
            <span style="background-color: #4fc3f7; padding: 2px 5px; border: 1px solid #000;">M: MALAM</span>
            <span style="background-color: #ff5252; color: white; padding: 2px 5px; border: 1px solid #000;">O: OFF/LIBUR</span>
            <span style="background-color: #e0e0e0; padding: 2px 5px; border: 1px solid #000;">N: NON SHIFT</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5" rowspan="2">{{ strtoupper('No') }}</th>
                    <th width="25" rowspan="2">{{ strtoupper('Nama Personil') }}</th>
                    <th width="15" rowspan="2">{{ strtoupper('Jabatan') }}</th> 
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
    @else
         @if(isset($dataGabungan['shift']))
            <h2>8. {{ strtoupper('Laporan Jadwal Shift') }}</h2>
            <table><tr><td class="empty">Tidak ada data shift pada periode ini.</td></tr></table>
         @endif
    @endif

</body>
</html>