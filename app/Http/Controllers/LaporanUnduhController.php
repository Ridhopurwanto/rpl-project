<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Panggil SEMUA model yang Anda perlukan untuk laporan
use App\Models\Presensi;
use App\Models\Patroli;
use App\Models\Shift;
use App\Models\Tamu;       // Pastikan Anda punya Model ini
use App\Models\Barang;    // Pastikan Anda punya Model ini
use App\Models\Kendaraan; // Pastikan Anda punya Model ini
use App\Models\GangguanKamtibmas;  // Pastikan Anda punya Model ini

class LaporanUnduhController extends Controller
{
    /**
     * Menampilkan halaman 'Unduh Laporan'.
     */
    public function index()
    {
        // Hanya menampilkan view
        return view('laporan.unduh');
    }

    /**
     * Memproses permintaan download laporan gabungan.
     */
    public function download(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:harian,bulanan',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'laporan' => 'required|array|min:1', // Pastikan minimal 1 checkbox dipilih
            'format' => 'required|in:pdf,excel', // Menangkap tombol mana yang ditekan
        ]);

        $tanggalMulai = $request->date_from;
        $tanggalSelesai = $request->date_to;
        $laporanDiminta = $request->laporan; // Array: ['presensi', 'patroli', ...]
        $format = $request->format;

        // Siapkan array untuk menampung semua data
        $dataGabungan = [];
        $dataGabungan['tanggalMulai'] = $tanggalMulai;
        $dataGabungan['tanggalSelesai'] = $tanggalSelesai;

        // Ambil data dari database berdasarkan checkbox yang dipilih
        foreach ($laporanDiminta as $jenis) {
            switch ($jenis) {
                case 'presensi':
                    $dataGabungan['presensi'] = Presensi::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'patroli':
                    $dataGabungan['patroli'] = Patroli::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'tamu':
                    $dataGabungan['tamu'] = Tamu::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'barang':
                    $dataGabungan['barang'] = Barang::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'kendaraan':
                    $dataGabungan['kendaraan'] = Kendaraan::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'gangguan':
                    $dataGabungan['gangguan'] = GangguanKamtibmas::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
                case 'shift':
                    $dataGabungan['shift'] = Shift::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->get();
                    break;
            }
        }

        // --- PENTING: PROSES DOWNLOAD ---
        // Anda perlu meng-install package untuk ini, contoh: Laravel Excel
        // Jalankan: composer require maatwebsite/excel
        
        // (Contoh logika jika Anda menggunakan Laravel Excel)
        if ($format == 'excel') {
            // Anda harus membuat "Export Class"
            // php artisan make:export LaporanGabunganExport --model=Presensi
            // return (new LaporanGabunganExport($dataGabungan))->download('laporan_gabungan.xlsx');
            
            // Untuk sekarang, kita kembalikan JSON untuk tes
            return response()->json($dataGabungan);
        }

        // (Contoh logika jika Anda menggunakan Laravel DOMPDF)
        if ($format == 'pdf') {
            // Anda harus membuat view Blade khusus untuk PDF
            // $pdf = PDF::loadView('laporan.template_pdf', $dataGabungan);
            // return $pdf->download('laporan_gabungan.pdf');

            // Untuk sekarang, kita kembalikan JSON untuk tes
            return response()->json($dataGabungan);
        }
    }
}