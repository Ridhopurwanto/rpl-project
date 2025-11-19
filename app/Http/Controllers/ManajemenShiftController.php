<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ManajemenShiftController extends Controller
{
    /**
     * Menampilkan halaman kalender shift dengan layout manual (Grid).
     */
    public function index(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        // 1. Tentukan bulan yang akan ditampilkan (Default: bulan ini)
        // Jika ada input 'bulan' (format Y-m), pakai itu.
        $bulanRequest = $request->input('bulan', Carbon::now()->format('Y-m'));
        $tanggalAwal  = Carbon::createFromFormat('Y-m', $bulanRequest)->startOfMonth();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        // 2. Ambil data shift dari DB untuk bulan ini saja
        $shiftsDB = Shift::where('id_pengguna', $id_pengguna)
                         ->whereBetween('tanggal', [$tanggalAwal->toDateString(), $tanggalAkhir->toDateString()])
                         ->get()
                         ->keyBy('tanggal'); // Supaya mudah diakses array['2025-11-01']

        // 3. Generate Struktur Kalender (Mirip Presensi Anggota)
        $kalender = [];
        
        // Isi kotak kosong di awal bulan (jika tgl 1 bukan hari minggu)
        // dayOfWeek: 0 (Minggu) - 6 (Sabtu)
        $hariPertama = $tanggalAwal->dayOfWeek; 
        for ($i = 0; $i < $hariPertama; $i++) {
            $kalender[] = null; // Kotak kosong
        }

        // Isi tanggal
        $periode = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
        foreach ($periode as $date) {
            $tglStr = $date->toDateString();
            
            // Cek apakah ada shift di DB, jika tidak default 'Off' (atau kosong)
            $jenisShift = $shiftsDB[$tglStr]->jenis_shift ?? 'Off'; // Default Off jika belum diisi

            $kalender[] = [
                'tanggal' => $date->day, // 1, 2, 3...
                'full_date' => $tglStr, // 2025-11-01
                'jenis_shift' => $jenisShift
            ];
        }

        // Kirim data ke view
        return view('komandan.akun.shift', [
            'user' => $user,
            'kalender' => $kalender,
            'bulanTahun' => $tanggalAwal, // Untuk judul "NOVEMBER 2025"
            'prevMonth' => $tanggalAwal->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $tanggalAwal->copy()->addMonth()->format('Y-m'),
        ]);
    }

    /**
     * Update Shift (Logic tetap sama, hanya return JSON)
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal'     => 'required|date_format:Y-m-d',
            'jenis_shift' => 'required|in:Pagi,Malam,Off',
        ]);

        try {
            Shift::updateOrCreate(
                [
                    'id_pengguna' => $request->id_pengguna,
                    'tanggal'     => $request->tanggal,
                ],
                [
                    'jenis_shift' => $request->jenis_shift,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil diperbarui!',
                'jenis_shift' => $request->jenis_shift // Kirim balik untuk update UI
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}