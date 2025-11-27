<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftRule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Notifications\PerubahanShiftNotification;

class ManajemenShiftController extends Controller
{
    /**
     * Menampilkan halaman kalender shift.
     */
    public function index(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        // 1. Tentukan Rentang Waktu
        $bulanRequest = $request->input('bulan', Carbon::now()->format('Y-m'));
        $tanggalAwal  = Carbon::createFromFormat('Y-m', $bulanRequest)->startOfMonth();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        // 2. Ambil Data Shift
        $shiftsDB = Shift::with('shiftRule')
                         ->where('id_pengguna', $id_pengguna)
                         ->whereBetween('tanggal', [$tanggalAwal->toDateString(), $tanggalAkhir->toDateString()])
                         ->get()
                         ->keyBy('tanggal');

        // 3. Ambil Data Libur (Hybrid)
        $hariLibur = $this->getHariLibur($tanggalAwal->year);

        // 4. Render Kalender
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

        return view('komandan.akun.shift', [
            'user' => $user,
            'kalender' => $kalender,
            'bulanTahun' => $tanggalAwal,
            'prevMonth' => $tanggalAwal->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $tanggalAwal->copy()->addMonth()->format('Y-m'),
        ]);
    }

    /**
     * Update Shift dengan Logika Smart Auto-Fill & Force Update Libur
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal'     => 'required|date_format:Y-m-d',
            'jenis_shift' => 'required|in:Pagi,Malam,Off',
        ]);

        try {
            $user = User::findOrFail($request->id_pengguna);
            $targetDate = Carbon::parse($request->tanggal);
            
            // Loop sampai 6 bulan ke depan agar pola tidak putus
            $endDate = $targetDate->copy()->addMonths(6)->endOfMonth(); 

            // Ambil ID Shift dari DB
            $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Off'])
                              ->get()
                              ->pluck('idshift_rule', 'jenis_shift');

            $selectedShiftId = $rules[$request->jenis_shift];

            // 1. Simpan Tanggal Utama (User Click)
            $shift = Shift::updateOrCreate(
                ['id_pengguna' => $request->id_pengguna, 'tanggal' => $request->tanggal],
                ['jenis_shift' => $selectedShiftId]
            );

            $affectedDates = []; 
            $currentDate = $targetDate->copy()->addDay();
            $counter = 1;

            // Ambil Data Libur (Untuk Komandan)
            $hariLibur = $this->getHariLibur($targetDate->year, $endDate->year);

            // ==================================================
            // LOGIKA 1: ANGGOTA (Pola P-P-M-M-O-O)
            // ==================================================
            if (strtolower($user->peran) == 'anggota') {
                $pattern = [
                    $rules['Pagi'], $rules['Pagi'], 
                    $rules['Malam'], $rules['Malam'], 
                    $rules['Off'], $rules['Off']
                ];

                // Deteksi start index berdasarkan shift kemarin
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
                    // Anggota: Hanya isi jika kosong (Strict: Jangan timpa manual)
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
            // ==================================================
            // LOGIKA 2: KOMANDAN (Force Off saat Libur)
            // ==================================================
            elseif (strtolower($user->peran) == 'komandan') {
                while ($currentDate <= $endDate) {
                    $tglStr = $currentDate->format('Y-m-d');
                    
                    // Cek Kondisi Libur
                    $isOffDay = $currentDate->isWeekend() || in_array($tglStr, $hariLibur);
                    
                    // Ambil data lama (jika ada)
                    $existingShift = Shift::where('id_pengguna', $request->id_pengguna)
                                          ->whereDate('tanggal', $currentDate)
                                          ->first();

                    if ($isOffDay) {
                        // JIKA LIBUR: PAKSA JADI OFF (Meskipun sudah terisi Pagi)
                        if (!$existingShift || $existingShift->jenis_shift != $rules['Off']) {
                            Shift::updateOrCreate(
                                ['id_pengguna' => $request->id_pengguna, 'tanggal' => $tglStr],
                                ['jenis_shift' => $rules['Off']]
                            );
                            $affectedDates[] = ['date' => $tglStr, 'shift' => 'Off'];
                        }
                    } 
                    else {
                        // JIKA HARI KERJA: Hanya isi Pagi jika KOSONG
                        if (!$existingShift) {
                            Shift::create([
                                'id_pengguna' => $request->id_pengguna, 
                                'tanggal' => $tglStr, 
                                'jenis_shift' => $rules['Pagi']
                            ]);
                            $affectedDates[] = ['date' => $tglStr, 'shift' => 'Pagi'];
                        }
                    }
                    
                    $currentDate->addDay();
                }
            }

            // Kirim Notifikasi
            if ($shift->wasRecentlyCreated || $shift->wasChanged('jenis_shift')) {
                 $aksi = $shift->wasRecentlyCreated ? 'dibuatkan' : 'diubah';
                 $pesan = "Jadwal shift tanggal " . $request->tanggal . " telah {$aksi}.";
                 if ($user) $user->notify(new PerubahanShiftNotification($pesan, $shift));
            }

            return response()->json([
                'success' => true,
                'message' => 'Shift tersimpan. Pola & Libur diterapkan!',
                'affected_dates' => $affectedDates
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper: Ambil Hari Libur (Hybrid: API + Manual Backup)
     */
    private function getHariLibur($tahunAwal, $tahunAkhir = null)
    {
        $years = [$tahunAwal];
        if ($tahunAkhir && $tahunAkhir != $tahunAwal) {
            $years[] = $tahunAkhir;
        }

        // --- DAFTAR LIBUR MANUAL ---
        // Digunakan jika API gagal ATAU untuk request khusus
        $manualHolidays = [
            '2025-01-01', '2025-01-27', '2025-01-29', 
            '2025-03-29', '2025-03-31', '2025-04-01', 
            '2025-04-18', '2025-04-20', '2025-05-01', 
            '2025-05-12', '2025-05-29', '2025-06-01', 
            '2025-06-06', '2025-06-27', '2025-08-17', 
            '2025-09-05', '2025-12-25',

            // --- 2026 (Manual Request) ---
            '2026-03-18', // Request khusus
            '2026-03-19', // Nyepi 2026
        ];

        $allHolidays = [];

        foreach ($years as $year) {
            // Gunakan Cache v7 agar data lama ter-reset
            $liburTahunIni = Cache::remember("holidays_v7_{$year}", 43200, function () use ($year, $manualHolidays) {
                try {
                    $response = Http::timeout(2)->get("https://dayoffapi.vercel.app/api?year={$year}");
                    if ($response->successful()) {
                        $apiData = $response->json();
                        $apiDates = array_column($apiData, 'tanggal');
                        return array_unique(array_merge($apiDates, $manualHolidays));
                    }
                } catch (\Exception $e) {}

                return $manualHolidays;
            });

            $allHolidays = array_merge($allHolidays, $liburTahunIni);
        }

        return $allHolidays;
    }
}