<?php

namespace App\Http\Controllers\Komandan;

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
            
            if (isset($shiftsDB[$tglStr]->shiftRule) && $shiftsDB[$tglStr]->shiftRule->idshift_rule == 4) {
                $jenisShift = 'Pagi';
            }

            $isLibur = in_array($tglStr, $hariLibur);

            $kalender[] = [
                'tanggal' => $date->day,
                'full_date' => $tglStr,
                'jenis_shift' => $jenisShift,
                'is_libur' => $isLibur
            ];
        }

        return view('komandan.akun.shift', [
            'user' => $user,
            'kalender' => $kalender,
            'bulanTahun' => $tanggalAwal,
            'prevMonth' => $tanggalAwal->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $tanggalAwal->copy()->addMonth()->format('Y-m'),
        ]);
    }

     
    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal'     => 'required|date_format:Y-m-d',
            'jenis_shift' => 'required|in:Pagi,Malam,Off',
            'apply_pattern' => 'boolean'
        ]);

        try {
            $user = User::findOrFail($request->id_pengguna);
            $targetDate = Carbon::parse($request->tanggal);
            
            
            $endDate = $targetDate->copy()->addMonths(6)->endOfMonth(); 

            
            $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Off'])
                              ->get()
                              ->pluck('idshift_rule', 'jenis_shift');

            $selectedShiftId = $rules[$request->jenis_shift];

            if (($user->jenis_jadwal == 'non_shift' || $user->peran == 'non_shift') && $request->jenis_shift != 'Off') {
                 $selectedShiftId = 4; 
            }
            
            $shift = Shift::updateOrCreate(
                ['id_pengguna' => $request->id_pengguna, 'tanggal' => $request->tanggal],
                ['jenis_shift' => $selectedShiftId]
            );

            $affectedDates = [];

            
            if ($request->apply_pattern) {
                
                Shift::where('id_pengguna', $request->id_pengguna)
                     ->whereDate('tanggal', '>', $targetDate)
                     ->delete();

                $currentDate = $targetDate->copy()->addDay();
                $counter = 1;

                
                $hariLibur = $this->getHariLibur($targetDate->year, $endDate->year);

                
                $jadwalType = $user->jenis_jadwal; 
                if (!$jadwalType) {
                     $jadwalType = ($user->peran == 'anggota') ? 'shift' : 'non_shift';
                }

                
                if ($jadwalType == 'shift') {
                    $pattern = [
                        $rules['Pagi'], $rules['Pagi'], 
                        $rules['Malam'], $rules['Malam'], 
                        $rules['Off'], $rules['Off']
                    ];

                    
                    $startIndex = 0; 
                    if ($request->jenis_shift == 'Pagi') {
                        $yesterday = Shift::where('id_pengguna', $request->id_pengguna)->whereDate('tanggal', $targetDate->copy()->subDay())->first();
                        $startIndex = ($yesterday && $yesterday->jenis_shift == $rules['Pagi']) ? 1 : 0;
                    }
                    elseif ($request->jenis_shift == 'Malam') {
                        $yesterday = Shift::where('id_pengguna', $request->id_pengguna)->whereDate('tanggal', $targetDate->copy()->subDay())->first();
                        $startIndex = ($yesterday && $yesterday->jenis_shift == $rules['Malam']) ? 3 : 2;
                    }
                    elseif ($request->jenis_shift == 'Off') {
                        $yesterday = Shift::where('id_pengguna', $request->id_pengguna)->whereDate('tanggal', $targetDate->copy()->subDay())->first();
                        $startIndex = ($yesterday && $yesterday->jenis_shift == $rules['Off']) ? 5 : 4;
                    }

                    while ($currentDate <= $endDate) {
                        $exists = Shift::where('id_pengguna', $request->id_pengguna)
                                       ->whereDate('tanggal', $currentDate)
                                       ->exists();
                        
                        if (!$exists) {
                            $patternIndex = ($startIndex + $counter) % 6;
                            $newShiftId = $pattern[$patternIndex];
                            $shiftName = $rules->search($newShiftId); 

                            Shift::create([
                                'id_pengguna' => $request->id_pengguna, 
                                'tanggal' => $currentDate->format('Y-m-d'), 
                                'jenis_shift' => $newShiftId
                            ]);
                            $affectedDates[] = ['date' => $currentDate->format('Y-m-d'), 'shift' => $shiftName];
                        }
                        $currentDate->addDay();
                        $counter++;
                    }
                }
                
                elseif ($jadwalType == 'non_shift') {
                    while ($currentDate <= $endDate) {
                        $tglStr = $currentDate->format('Y-m-d');
                        $isOffDay = $currentDate->isWeekend() || in_array($tglStr, $hariLibur);
                        
                        $exists = Shift::where('id_pengguna', $request->id_pengguna)
                                       ->whereDate('tanggal', $currentDate)
                                       ->exists();

                        if (!$exists) {
                        if ($isOffDay) {
                                Shift::create(['id_pengguna' => $request->id_pengguna, 'tanggal' => $tglStr, 'jenis_shift' => $rules['Off']]);
                                $affectedDates[] = ['date' => $tglStr, 'shift' => 'Off'];
                            } else {
                                Shift::create(['id_pengguna' => $request->id_pengguna, 'tanggal' => $tglStr, 'jenis_shift' => 4]);
                                $affectedDates[] = ['date' => $tglStr, 'shift' => 'Pagi'];
                            }
                        }
                        $currentDate->addDay();
                    }
                }
            }

            if ($shift->wasRecentlyCreated || $shift->wasChanged('jenis_shift')) {
                 $aksi = $shift->wasRecentlyCreated ? 'dibuatkan' : 'diubah';
                 
                 if ($shift->wasRecentlyCreated) {
                     $pesan = "Jadwal shift Anda telah diatur oleh Komandan sampai periode tertentu.";
                 } else {
                     $pesan = "Jadwal shift Anda pada tanggal {$request->tanggal} telah diubah menjadi {$request->jenis_shift}.";
                 }
                 
                 $shift->load('shiftRule'); 

                 try {
                    $type = $shift->wasRecentlyCreated ? 'assignment' : 'change';
                    $user->notify(new PerubahanShiftNotification($pesan, $shift, $type));
                 } catch (\Exception $e) {
                    Log::error('Gagal kirim notifikasi shift: ' . $e->getMessage());
                 }
            }

            return response()->json([
                'success' => true,
                'message' => $request->apply_pattern ? 'Shift diubah & pola masa depan diperbarui!' : 'Shift berhasil disimpan.',
                'affected_dates' => $affectedDates
            ]);       

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

     
    public function reset(Request $request)
    {
        $request->validate(['id_pengguna' => 'required']);

        try {
            Shift::where('id_pengguna', $request->id_pengguna)
                 ->where('tanggal', '>', Carbon::today())
                 ->delete();

            return response()->json(['success' => true, 'message' => 'Jadwal masa depan berhasil dikosongkan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal reset: ' . $e->getMessage()], 500);
        }
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