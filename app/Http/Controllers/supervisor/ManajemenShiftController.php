<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftRule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Notifications\PerubahanShiftNotification;

class ManajemenShiftController extends Controller
{
     
    public function index(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        
        $bulanRequest = $request->input('bulan', Carbon::now()->format('Y-m'));
        $tanggalAwal  = Carbon::createFromFormat('Y-m', $bulanRequest)->startOfMonth();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        
        $shiftsDB = Shift::with('shiftRule')
                         ->where('id_pengguna', $id_pengguna)
                         ->whereBetween('tanggal', [$tanggalAwal->toDateString(), $tanggalAkhir->toDateString()])
                         ->get()
                         ->keyBy('tanggal');

        
        $hariLibur = $this->getHariLibur($tanggalAwal->year);

        
        $kalender = [];
        
        
        $hariPertama = $tanggalAwal->dayOfWeek; 
        for ($i = 0; $i < $hariPertama; $i++) {
            $kalender[] = null;
        }

        $periode = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
        foreach ($periode as $date) {
            $tglStr = $date->toDateString();
            
            $jenisShift = $shiftsDB[$tglStr]->shiftRule->jenis_shift ?? 'Off'; 
            $isLibur = in_array($tglStr, $hariLibur);

            $kalender[] = [
                'tanggal' => $date->day,
                'full_date' => $tglStr,
                'jenis_shift' => $jenisShift,
                'is_libur' => $isLibur
            ];
        }

        return view('supervisor.akun.shift', [
            'user' => $user,
            'kalender' => $kalender,
            'bulanTahun' => $tanggalAwal,
            'prevMonth' => $tanggalAwal->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $tanggalAwal->copy()->addMonth()->format('Y-m'),
        ]);
    }



     
     
    private function getHariLibur($tahunAwal, $tahunAkhir = null)
    {
        
        
        $manualHolidays = [
            
            '2025-01-01', 
            '2025-01-27', 
            '2025-01-29', 
            '2025-03-29', 
            '2025-03-31', 
            '2025-04-01', 
            '2025-04-18', 
            '2025-04-20', 
            '2025-05-01', 
            '2025-05-12', 
            '2025-05-29', 
            '2025-06-01', 
            '2025-06-06', 
            '2025-06-27', 
            '2025-08-17', 
            '2025-09-05', 
            '2025-12-25', 

            
            '2026-01-01', 
            '2026-01-16', 
            '2026-02-17', 
            '2026-03-19', 
            '2026-03-20', 
            '2026-03-21', 
            '2026-04-03', 
            '2026-04-05', 
            '2026-05-01', 
            '2026-05-14', 
            '2026-05-27', 
            '2026-05-31', 
            '2026-06-01', 
            '2026-06-16', 
            '2026-08-17', 
            '2026-08-25', 
            '2026-12-25', 
        ];

        
        
        $googleCalendarUrl = 'https://calendar.google.com/calendar/ical/en.indonesian%23holiday%40group.v.calendar.google.com/public/basic.ics';
        
        $fetchedHolidays = [];

        try {
            
            $response = Http::timeout(5)->get($googleCalendarUrl);

            if ($response->successful()) {
                $icsContent = $response->body();
                
                
                
                preg_match_all('/DTSTART(?:;VALUE=DATE)?:(\d{8})/', $icsContent, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $dateStr) {
                        
                        $formattedDate = substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
                        $fetchedHolidays[] = $formattedDate;
                    }
                }
            }
        } catch (\Exception $e) {
            
            
        }

        
        
        $allHolidays = array_unique(array_merge($manualHolidays, $fetchedHolidays));
        
        return $allHolidays;
    }
}