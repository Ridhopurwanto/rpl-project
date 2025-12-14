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



<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
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
        <tr>
            <td colspan="{{ count($period) + 3 }}" style="border: none; height: 15px;"></td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: 1px solid #000000; background-color: #ffff00; text-align: center; font-weight: bold;">P</td>
            <td colspan="{{ count($period) + 1 }}" style="border: none; vertical-align: middle; padding-left: 10px;">SHIFT PAGI</td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: 1px solid #000000; background-color: #4fc3f7; text-align: center; font-weight: bold;">M</td>
            <td colspan="{{ count($period) + 1 }}" style="border: none; vertical-align: middle; padding-left: 10px;">SHIFT MALAM</td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="border: 1px solid #000000; background-color: #ff5252; color: white; text-align: center; font-weight: bold;">O</td>
            <td colspan="{{ count($period) + 1 }}" style="border: none; vertical-align: middle; padding-left: 10px;">OFF</td>
        </tr>
    </tbody>
</table>
