<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    LAPORAN SHIFT ANGGOTA
</h2>

<div style="margin-bottom: 15px; font-size: 10pt; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd;">
    <strong>KETERANGAN:</strong>
    <div style="margin-top: 8px;">
        <span style="display: inline-block; margin-right: 20px;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #ffff00; border: 1px solid #000; margin-right: 5px; vertical-align: middle;"></span>
            <span>P = PAGI</span>
        </span>
        <span style="display: inline-block; margin-right: 20px;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #4fc3f7; border: 1px solid #000; margin-right: 5px; vertical-align: middle;"></span>
            <span>M = MALAM</span>
        </span>
        <span style="display: inline-block; margin-right: 20px;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #ff5252; border: 1px solid #000; margin-right: 5px; vertical-align: middle; color: white;"></span>
            <span>O = OFF/LIBUR</span>
        </span>
        <span style="display: inline-block;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #e0e0e0; border: 1px solid #000; margin-right: 5px; vertical-align: middle;"></span>
            <span>N = NON SHIFT</span>
        </span>
    </div>
</div>

@php
    $start = \Carbon\Carbon::parse($tanggalMulai);
    $end = \Carbon\Carbon::parse($tanggalSelesai);
    $period = \Carbon\CarbonPeriod::create($start, $end);

    $shiftMatrix = [];
    $userDetails = [];

    foreach($shift ?? [] as $s) {
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

        // 1. Ambil object rule dari relasi
        $rule = $s->shiftRule; 
        
        // 2. Ambil teks jenisnya (Pagi, Malam, Off)
        $namaShift = $rule ? $rule->jenis_shift : '-'; 

        // 3. Ambil Kode Huruf Depan (P, M, O, N)
        $kode = substr($namaShift, 0, 1); 
        if ($namaShift == 'Non Shift') $kode = 'N';
        
        $shiftMatrix[$userId][$dateKey] = $kode;
    }
    
@endphp

<table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 7pt;">
    <thead>
        <tr style="background-color: #cccccc;">
            <th rowspan="2" style="border: 1px solid #000; padding: 3px; text-align: center; width: 3%">NO</th>
            <th rowspan="2" style="border: 1px solid #000; padding: 3px; text-align: left; width: 15%">NAMA PERSONIL</th>
            <th rowspan="2" style="border: 1px solid #000; padding: 3px; text-align: center; width: 10%">JABATAN</th>
            @foreach($period as $date)
                <th style="border: 1px solid #000; padding: 2px; text-align: center; font-size: 7pt;">{{ $date->format('d') }}</th>
            @endforeach
        </tr>
        <tr style="background-color: #cccccc;">
            @foreach($period as $date)
                @php
                    $dayMap = [
                        'Mon' => 'SEN', 'Tue' => 'SEL', 'Wed' => 'RAB', 
                        'Thu' => 'KAM', 'Fri' => 'JUM', 'Sat' => 'SAB', 'Sun' => 'MIN'
                    ];
                    $dayEng = $date->format('D');
                    $dayInd = $dayMap[$dayEng] ?? $dayEng;
                @endphp
                <th style="border: 1px solid #000; padding: 1px; text-align: center; font-size: 6pt;">
                    {{ $dayInd }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($userDetails as $userId => $info)
            <tr>
                <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000; padding: 3px; font-size: 7pt;">{{ $info['name'] }}</td>
                <td style="border: 1px solid #000; padding: 3px; text-align: center; font-size: 7pt;">{{ $info['role'] }}</td>
                
                @foreach($period as $date)
                    @php
                        $dKey = $date->format('Y-m-d');
                        $kode = $shiftMatrix[$userId][$dKey] ?? '';
                        
                        $bgColor = '#fff';
                        if($kode == 'P') $bgColor = '#ffff00';
                        elseif($kode == 'M') $bgColor = '#4fc3f7';
                        elseif($kode == 'O') $bgColor = '#ff5252';
                        elseif($kode == 'N') $bgColor = '#e0e0e0';
                    @endphp

                    <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: {{ $bgColor }}; font-weight: bold; font-size: 7pt;">
                        {{ $kode }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
