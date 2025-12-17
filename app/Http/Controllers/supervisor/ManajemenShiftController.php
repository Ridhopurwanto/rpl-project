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

        // 2. Ambil Data Shift dari DB
        $shiftsDB = Shift::with('shiftRule')
                         ->where('id_pengguna', $id_pengguna)
                         ->whereBetween('tanggal', [$tanggalAwal->toDateString(), $tanggalAkhir->toDateString()])
                         ->get()
                         ->keyBy('tanggal');

        // 3. Ambil Data Libur (Google Calendar + Manual)
        $hariLibur = $this->getHariLibur($tanggalAwal->year);

        // 4. Render Kalender
        $kalender = [];
        
        // Padding hari kosong di awal bulan
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



    /**
     * Helper: Ambil Hari Libur (Google Calendar + Manual Backup)
     * TANPA CACHE agar aman di semua laptop.
     */
    /**
     * Helper: Ambil Hari Libur (Google Calendar + Manual Backup Lengkap)
     */
    private function getHariLibur($tahunAwal, $tahunAkhir = null)
    {
        // 1. DATA LIBUR MANUAL LENGKAP (2025 & PREDIKSI 2026)
        // Sumber: SKB 3 Menteri & Kalender Hijriah
        $manualHolidays = [
            // --- TAHUN 2025 ---
            '2025-01-01', // Tahun Baru Masehi
            '2025-01-27', // Isra Mikraj
            '2025-01-29', // Tahun Baru Imlek
            '2025-03-29', // Hari Suci Nyepi
            '2025-03-31', // Idul Fitri 1446 H
            '2025-04-01', // Idul Fitri 1446 H
            '2025-04-18', // Wafat Yesus Kristus
            '2025-04-20', // Paskah (Minggu)
            '2025-05-01', // Hari Buruh
            '2025-05-12', // Hari Raya Waisak
            '2025-05-29', // Kenaikan Yesus Kristus
            '2025-06-01', // Hari Lahir Pancasila
            '2025-06-06', // Idul Adha 1446 H
            '2025-06-27', // Tahun Baru Islam 1447 H
            '2025-08-17', // HUT RI
            '2025-09-05', // Maulid Nabi Muhammad SAW
            '2025-12-25', // Hari Raya Natal

            // --- TAHUN 2026 (PREDIKSI) ---
            '2026-01-01', // Tahun Baru Masehi
            '2026-01-16', // Isra Mikraj (Estimasi)
            '2026-02-17', // Tahun Baru Imlek (Estimasi)
            '2026-03-19', // Hari Suci Nyepi (Estimasi)
            '2026-03-20', // Idul Fitri 1447 H (Estimasi Hari 1)
            '2026-03-21', // Idul Fitri 1447 H (Estimasi Hari 2)
            '2026-04-03', // Wafat Yesus Kristus
            '2026-04-05', // Paskah
            '2026-05-01', // Hari Buruh
            '2026-05-14', // Kenaikan Yesus Kristus
            '2026-05-27', // Idul Adha 1447 H (Estimasi)
            '2026-05-31', // Hari Raya Waisak (Estimasi)
            '2026-06-01', // Hari Lahir Pancasila
            '2026-06-16', // Tahun Baru Islam 1448 H (Estimasi)
            '2026-08-17', // HUT RI
            '2026-08-25', // Maulid Nabi Muhammad SAW (Estimasi)
            '2026-12-25', // Hari Raya Natal
        ];

        // 2. FETCH DARI GOOGLE CALENDAR (Live Update)
        // URL ini sering update otomatis jika pemerintah mengubah jadwal
        $googleCalendarUrl = 'https://calendar.google.com/calendar/ical/en.indonesian%23holiday%40group.v.calendar.google.com/public/basic.ics';
        
        $fetchedHolidays = [];

        try {
            // Naikkan timeout jadi 5 detik agar tidak gampang gagal
            $response = Http::timeout(5)->get($googleCalendarUrl);

            if ($response->successful()) {
                $icsContent = $response->body();
                
                // Regex untuk menangkap tanggal (DTSTART;VALUE=DATE:yyyymmdd)
                // Kita juga tambahkan regex cadangan untuk format DTSTART:yyyymmdd (tanpa VALUE=DATE)
                preg_match_all('/DTSTART(?:;VALUE=DATE)?:(\d{8})/', $icsContent, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $dateStr) {
                        // Ubah 20250101 -> 2025-01-01
                        $formattedDate = substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
                        $fetchedHolidays[] = $formattedDate;
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika internet mati atau Google timeout, abaikan error dan pakai manual saja
            // Log error jika perlu: \Log::error("Gagal fetch libur: " . $e->getMessage());
        }

        // 3. MERGE DATA
        // Gabungkan manual + hasil fetch, lalu hapus duplikat
        $allHolidays = array_unique(array_merge($manualHolidays, $fetchedHolidays));
        
        return $allHolidays;
    }
}