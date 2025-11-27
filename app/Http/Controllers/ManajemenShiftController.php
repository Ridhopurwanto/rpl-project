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

        $bulanRequest = $request->input('bulan', Carbon::now()->format('Y-m'));
        $tanggalAwal  = Carbon::createFromFormat('Y-m', $bulanRequest)->startOfMonth();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        $shiftsDB = Shift::with('shiftRule')
                         ->where('id_pengguna', $id_pengguna)
                         ->whereBetween('tanggal', [$tanggalAwal->toDateString(), $tanggalAkhir->toDateString()])
                         ->get()
                         ->keyBy('tanggal');

        // Ambil Data Libur (Hybrid)
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

        return view('komandan.akun.shift', [
            'user' => $user,
            'kalender' => $kalender,
            'bulanTahun' => $tanggalAwal,
            'prevMonth' => $tanggalAwal->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $tanggalAwal->copy()->addMonth()->format('Y-m'),
        ]);
    }

    /**
     * Update Shift Smart Auto-Fill
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
            
            // Loop sampai 6 bulan ke depan
            $endDate = $targetDate->copy()->addMonths(6)->endOfMonth(); 

            $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Off'])
                              ->get()
                              ->pluck('idshift_rule', 'jenis_shift');

            $selectedShiftId = $rules[$request->jenis_shift];

            // Simpan Data Utama
            $shift = Shift::updateOrCreate(
                ['id_pengguna' => $request->id_pengguna, 'tanggal' => $request->tanggal],
                ['jenis_shift' => $selectedShiftId]
            );

            $affectedDates = []; 
            $currentDate = $targetDate->copy()->addDay();
            $counter = 1;

            // Ambil Hari Libur (Range Tahun)
            $hariLibur = $this->getHariLibur($targetDate->year, $endDate->year);

            // === LOGIKA ANGGOTA ===
            if (strtolower($user->peran) == 'anggota') {
                $pattern = [
                    $rules['Pagi'], $rules['Pagi'], 
                    $rules['Malam'], $rules['Malam'], 
                    $rules['Off'], $rules['Off']
                ];

                $startIndex = 0; 
                if ($request->jenis_shift == 'Pagi') {
                    $yesterday = Shift::where('id_pengguna', $request->id_pengguna)
                                      ->whereDate('tanggal', $targetDate->copy()->subDay())
                                      ->first();
                    $startIndex = ($yesterday && $yesterday->jenis_shift == $rules['Pagi']) ? 1 : 0;
                }
                elseif ($request->jenis_shift == 'Malam') {
                    $yesterday = Shift::where('id_pengguna', $request->id_pengguna)
                                      ->whereDate('tanggal', $targetDate->copy()->subDay())
                                      ->first();
                    $startIndex = ($yesterday && $yesterday->jenis_shift == $rules['Malam']) ? 3 : 2;
                }
                elseif ($request->jenis_shift == 'Off') {
                    $yesterday = Shift::where('id_pengguna', $request->id_pengguna)
                                      ->whereDate('tanggal', $targetDate->copy()->subDay())
                                      ->first();
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
                            'tanggal'     => $currentDate->format('Y-m-d'),
                            'jenis_shift' => $newShiftId
                        ]);

                        $affectedDates[] = ['date' => $currentDate->format('Y-m-d'), 'shift' => $shiftName];
                    }
                    $currentDate->addDay();
                    $counter++;
                }
            }
            // === LOGIKA KOMANDAN ===
            elseif (strtolower($user->peran) == 'komandan') {
                while ($currentDate <= $endDate) {
                    $exists = Shift::where('id_pengguna', $request->id_pengguna)
                                   ->whereDate('tanggal', $currentDate)
                                   ->exists();

                    if (!$exists) {
                        $tglStr = $currentDate->format('Y-m-d');
                        
                        // Cek Sabtu/Minggu ATAU Hari Libur
                        $isOffDay = $currentDate->isWeekend() || in_array($tglStr, $hariLibur);
                        
                        $newShiftId = $isOffDay ? $rules['Off'] : $rules['Pagi'];
                        $shiftName  = $isOffDay ? 'Off' : 'Pagi';

                        Shift::create([
                            'id_pengguna' => $request->id_pengguna,
                            'tanggal'     => $tglStr,
                            'jenis_shift' => $newShiftId
                        ]);

                        $affectedDates[] = ['date' => $tglStr, 'shift' => $shiftName];
                    }
                    $currentDate->addDay();
                }
            }

            // Notifikasi (Opsional)
            if ($shift->wasRecentlyCreated || $shift->wasChanged('jenis_shift')) {
                 $aksi = $shift->wasRecentlyCreated ? 'dibuatkan' : 'diubah';
                 $pesan = "Jadwal shift Anda tanggal " . $request->tanggal . " telah **{$aksi}** menjadi **{$request->jenis_shift}**.";
                 if ($user) $user->notify(new PerubahanShiftNotification($pesan, $shift));
            }

            return response()->json([
                'success' => true,
                'message' => 'Shift tersimpan. Pola otomatis diterapkan!',
                'jenis_shift' => $request->jenis_shift,
                'affected_dates' => $affectedDates
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper: Ambil Hari Libur (Hybrid: API + Manual Backup)
     * Ini menjamin data libur selalu ada meskipun API mati.
     */
    private function getHariLibur($tahunAwal, $tahunAkhir = null)
    {
        $years = [$tahunAwal];
        if ($tahunAkhir && $tahunAkhir != $tahunAwal) {
            $years[] = $tahunAkhir;
        }

        // DAFTAR MANUAL HARUS BENAR
        $manualHolidays = [
            // --- 2024 (Sisa) ---
            '2024-12-25', '2024-12-26',
            
            // --- 2025 (SKB 3 Menteri) ---
            '2025-01-01', '2025-01-27', '2025-01-29', 
            '2025-03-29', // Nyepi 2025
            '2025-03-31', '2025-04-01', 
            '2025-04-18', '2025-04-20',
            '2025-05-01', '2025-05-12', '2025-05-29', 
            '2025-06-01', '2025-06-06', '2025-06-27', 
            '2025-08-17', '2025-09-05', '2025-12-25',

            // --- 2026 (Estimasi & Request Kamu) ---
            '2026-01-01', // Tahun Baru
            '2026-02-17', // Imlek (Estimasi)
            '2026-03-18', // <--- REQUEST KAMU (Anggap Cuti Bersama Nyepi)
            '2026-03-19', // Nyepi 2026 (Tanggal Asli)
            '2026-03-20', // Idul Fitri (Estimasi)
            '2026-03-21', // Idul Fitri (Estimasi)
            '2026-04-03', // Jumat Agung
            '2026-05-01', // Buruh
            '2026-05-14', // Kenaikan Isa Almasih
            '2026-06-01', // Pancasila
            '2026-08-17', // HUT RI
            '2026-12-25', // Natal
        ];

        $allHolidays = [];

        foreach ($years as $year) {
            // SAYA UBAH KEY CACHE JADI 'holidays_v6_' (Naik versi biar refresh cache)
            $liburTahunIni = Cache::remember("holidays_v6_{$year}", 43200, function () use ($year, $manualHolidays) {
                try {
                    // Coba Request API (Siapa tau API sudah update 2026)
                    $response = Http::timeout(2)->get("https://dayoffapi.vercel.app/api?year={$year}");
                    if ($response->successful()) {
                        $apiData = $response->json();
                        $apiDates = array_column($apiData, 'tanggal');
                        return array_unique(array_merge($apiDates, $manualHolidays));
                    }
                } catch (\Exception $e) {}

                // Kalau API gagal/kosong, pakai manual
                return $manualHolidays;
            });

            $allHolidays = array_merge($allHolidays, $liburTahunIni);
        }

        return $allHolidays;
    }
}