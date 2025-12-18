@php
    $start = \Carbon\Carbon::parse($meta['tanggalMulai']);
    $end = \Carbon\Carbon::parse($meta['tanggalSelesai']);
    
    // Split period by Month
    $months = [];
    $current = $start->copy()->startOfMonth();
    $endMonth = $end->copy()->endOfMonth(); // Ensure we cover the full range logic if needed, but strict to user range
    
    // Adjust logic: We want to iterate months that fall within the range
    $paramStart = $start->copy();
    $paramEnd = $end->copy();

    // Generate Month keys
    while ($paramStart <= $paramEnd) {
        $key = $paramStart->format('Y-m');
        if (!in_array($key, $months)) {
            $months[] = $key;
        }
        $paramStart->addMonth();
    }
@endphp

@foreach($months as $monthKey)
    @php
        $monthParams = \Carbon\Carbon::createFromFormat('Y-m', $monthKey);
        $monthName = $monthParams->isoFormat('MMMM Y');
        
        // Calculate start and end date FOR THIS MONTH within the global range
        $monthStart = $monthParams->copy()->startOfMonth();
        $monthEnd = $monthParams->copy()->endOfMonth();
        
        // Cap start/end if they exceed the global range
        if ($monthStart < $start) $monthStart = $start->copy();
        if ($monthEnd > $end) $monthEnd = $end->copy();
        
        $monthPeriod = \Carbon\CarbonPeriod::create($monthStart, $monthEnd);
    @endphp

    {{-- TABLE HEADER FOR THIS MONTH --}}
    <table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000; margin-bottom: 20px;">
        <thead>
            <tr>
                <td colspan="{{ count($monthPeriod) + 3 }}" style="font-weight: bold; font-size: 11pt; border: none; padding-bottom: 5px;">
                    BULAN: {{ strtoupper($monthName) }}
                </td>
            </tr>
            <tr>
                <th rowspan="2" style="{{ $thStyle }}" width="5">NO</th>
                <th rowspan="2" style="{{ $thStyle }}" width="25">NAMA PERSONIL</th>
                <th rowspan="2" style="{{ $thStyle }}" width="15">JABATAN</th> 
                @foreach($monthPeriod as $date)
                    <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 4;" width="4">{{ $date->format('d') }}</th>
                @endforeach
            </tr>
            <tr>
                 @foreach($monthPeriod as $date)
                    <th style="border: 1px solid #000000; text-align: center; font-size: 8pt; height: 15px;">
                        {{ strtoupper(substr($date->isoFormat('dddd'), 0, 3)) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            {{-- Loop ALL Users (Data passed from controller is Collection of Users) --}}
            @foreach($data as $user)
                <tr>
                    <td style="{{ $tdCenterStyle }}">{{ $no++ }}</td>
                    <td style="{{ $tdStyle }}">{{ $user->nama_lengkap }}</td>
                    <td style="{{ $tdCenterStyle }}">{{ ucfirst($user->peran) }}</td> 
                    
                    @foreach($monthPeriod as $date)
                        @php
                            $dKey = $date->format('Y-m-d');
                            // Check user's map
                            $shift = $user->shifts_by_date[$dKey] ?? null;
                            
                            $kode = 'O'; // Default OFF
                            $style = "text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #ff5252; color: #ffffff;"; // Default Red for OFF

                            if ($shift) {
                                $namaShift = $shift->shiftRule->jenis_shift ?? 'Off';
                                $kode = strtoupper(substr($namaShift, 0, 1));
                                
                                $baseStyle = "text-align: center; border: 1px solid #000000; font-weight: bold;";
                                if($kode == 'P') $style = $baseStyle . "background-color: #ffff00; color: #000000;";      
                                elseif($kode == 'M') $style = $baseStyle . "background-color: #4fc3f7; color: #000000;"; 
                                else $style = $baseStyle . "background-color: #ff5252; color: #ffffff;"; // Fallback/Off
                            }
                        @endphp
    
                        <td style="{{ $style }}">{{ $kode }}</td>
                    @endforeach
                </tr>
            @endforeach
            
            {{-- LEGEND (Only show at the bottom of the last month/table OR every table? Logic implies per table or end. Let's put per table for clarity or just end.) --}}
            {{-- User asked "legendanya paling bawah", implies bottom of sheet. But since we use one sheet, let's put it after each table or just once. --}}
            {{-- Let's put it after each table for clarity if 'dipisah tabel'. --}}
            <tr>
                <td colspan="{{ count($monthPeriod) + 3 }}" style="border: none; height: 10px;"></td>
            </tr>
        </tbody>
    </table>
@endforeach

{{-- GLOBAL LEGEND AT THE BOTTOM --}}
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="border: none;" width="5"></td>
        <td style="border: 1px solid #000000; background-color: #ffff00; text-align: center; font-weight: bold;" width="5">P</td>
        <td style="border: none; vertical-align: middle; padding-left: 10px;">SHIFT PAGI</td>
    </tr>
    <tr>
        <td style="border: none;"></td>
        <td style="border: 1px solid #000000; background-color: #4fc3f7; text-align: center; font-weight: bold;">M</td>
        <td style="border: none; vertical-align: middle; padding-left: 10px;">SHIFT MALAM</td>
    </tr>
    <tr>
        <td style="border: none;"></td>
        <td style="border: 1px solid #000000; background-color: #ff5252; color: white; text-align: center; font-weight: bold;">O</td>
        <td style="border: none; vertical-align: middle; padding-left: 10px;">OFF</td>
    </tr>
</table>
