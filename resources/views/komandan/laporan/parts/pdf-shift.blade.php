@php
    $start = \Carbon\Carbon::parse($tanggalMulai);
    $end = \Carbon\Carbon::parse($tanggalSelesai);
    
    // Split period by Month (Logic copied from Excel part)
    $months = [];
    $paramStart = $start->copy();
    $paramEnd = $end->copy();

    while ($paramStart <= $paramEnd) {
        $key = $paramStart->format('Y-m');
        if (!in_array($key, $months)) {
            $months[] = $key;
        }
        $paramStart->addMonth();
    }
@endphp

<h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
    LAPORAN SHIFT ANGGOTA
</h2>

{{-- Legend at Top or Bottom? User asked for bottom. But PDF usually has space constraints. Let's keep it consistent with Excel (Bottom each table or End). --}}
{{-- For PDF, let's put it once at the end. --}}

@foreach($months as $monthKey)
    @php
        $monthParams = \Carbon\Carbon::createFromFormat('Y-m', $monthKey);
        $monthName = $monthParams->isoFormat('MMMM Y');
        
        $monthStart = $monthParams->copy()->startOfMonth();
        $monthEnd = $monthParams->copy()->endOfMonth();
        
        if ($monthStart < $start) $monthStart = $start->copy();
        if ($monthEnd > $end) $monthEnd = $end->copy();
        
        $monthPeriod = \Carbon\CarbonPeriod::create($monthStart, $monthEnd);
    @endphp

    <h3 style="margin: 10px 10px; font-size: 10pt;">BULAN: {{ strtoupper($monthName) }}</h3>

    <table style="border-collapse: collapse; width: 98%; margin: 10px auto; font-size: 7pt;">
        <thead>
            <tr style="background-color: #cccccc;">
                <th rowspan="2" style="border: 1px solid #000; padding: 2px; text-align: center; width: 3%">NO</th>
                <th rowspan="2" style="border: 1px solid #000; padding: 2px; text-align: left; width: 15%">NAMA PERSONIL</th>
                <th rowspan="2" style="border: 1px solid #000; padding: 2px; text-align: center; width: 10%">JABATAN</th>
                @foreach($monthPeriod as $date)
                    <th style="border: 1px solid #000; padding: 1px; text-align: center; font-size: 6pt;">{{ $date->format('d') }}</th>
                @endforeach
            </tr>
            <tr style="background-color: #cccccc;">
                @foreach($monthPeriod as $date)
                    <th style="border: 1px solid #000; padding: 1px; text-align: center; font-size: 5pt;">
                        {{ strtoupper(substr($date->isoFormat('dddd'), 0, 1)) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            {{-- $shift passed from controller is actually the Collection of Users with shifts_by_date --}}
            {{-- Controller passes 'shift' => $users --}}
            @foreach($shift as $user)
                <tr>
                    <td style="border: 1px solid #000; padding: 2px; text-align: center;">{{ $no++ }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7pt;">{{ $user->nama_lengkap }}</td>
                    <td style="border: 1px solid #000; padding: 2px; text-align: center; font-size: 7pt;">{{ ucfirst($user->peran) }}</td>
                    
                    @foreach($monthPeriod as $date)
                        @php
                            $dKey = $date->format('Y-m-d');
                            $sObj = $user->shifts_by_date[$dKey] ?? null;
                            
                            $kode = 'O'; 
                            $bgColor = '#ff5252'; // Default Red

                            if ($sObj) {
                                $namaShift = $sObj->shiftRule->jenis_shift ?? 'Off';
                                $kode = strtoupper(substr($namaShift, 0, 1));
                                
                                if($kode == 'P') $bgColor = '#ffff00';
                                elseif($kode == 'M') $bgColor = '#4fc3f7';
                                else $bgColor = '#ff5252'; 
                            }
                        @endphp

                        <td style="border: 1px solid #000; padding: 1px; text-align: center; background-color: {{ $bgColor }}; font-weight: bold; font-size: 6pt; color: {{ $bgColor == '#ff5252' ? 'white' : 'black' }};">
                            {{ $kode }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- Page Break between months if needed, or just specific spacing --}}
    <div style="margin-bottom: 20px;"></div>
@endforeach

{{-- LEGEND (Bottom) --}}
<div style="margin: 10px 10px; font-size: 8pt; border: 1px solid #ccc; padding: 5px; width: 50%;">
    <strong>KETERANGAN:</strong>
    <table style="width: 100%; border: none; margin: 5px 0 0 0;">
        <tr>
            <td style="border: none; width: 25px;"><div style="width: 20px; height: 15px; background-color: #ffff00; border: 1px solid #000; text-align: center; font-size: 8pt;">P</div></td>
            <td style="border: none;">SHIFT PAGI</td>
        </tr>
        <tr>
            <td style="border: none;"><div style="width: 20px; height: 15px; background-color: #4fc3f7; border: 1px solid #000; text-align: center; font-size: 8pt;">M</div></td>
            <td style="border: none;">SHIFT MALAM</td>
        </tr>
        <tr>
            <td style="border: none;"><div style="width: 20px; height: 15px; background-color: #ff5252; color: white; border: 1px solid #000; text-align: center; font-size: 8pt;">O</div></td>
            <td style="border: none;">OFF / LIBUR</td>
        </tr>
    </table>
</div>
