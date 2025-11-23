<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// --- IMPORT MODEL YANG BENAR SESUAI DB ---
use App\Models\Presensi;
use App\Models\Patroli;
use App\Models\Shift;
use App\Models\Tamu;       
use App\Models\GangguanKamtibmas;

// Model Barang (Ada 2 Tabel)
use App\Models\BarangTemuan; 
use App\Models\BarangTitipan;

// Model Kendaraan (Gunakan Log untuk laporan keluar masuk)
use App\Models\LogKendaraan; 

use App\Exports\LaporanGabunganExport; 
use Maatwebsite\Excel\Facades\Excel;
use PDF; 

class LaporanUnduhController extends Controller
{
    public function index()
    {
        return view('komandan.unduh');
    }

    /**
     * DOWNLOAD GABUNGAN
     */
    public function download(Request $request)
    {
        $queue = json_decode($request->download_queue, true);
        $format = $request->format;

        if (!$queue || count($queue) === 0) {
            return back()->with('error', 'Tidak ada laporan dipilih');
        }

        // Setup Data Dasar
        $dataGabungan = [
            'tanggalMulai' => $queue[0]['dateStart'] ?? date('Y-m-d'),
            'tanggalSelesai' => $queue[0]['dateEnd'] ?? date('Y-m-d'),
        ];

        // Loop Antrian
        foreach ($queue as $item) {
            // Normalisasi nama: gangguan_kamtibmas -> gangguan, shift_anggota -> shift
            $jenis = $this->normalizeType($item['value']);
            
            $start = $item['dateStart'];
            $end = $item['dateEnd'];

            // Ambil data dan masukkan ke key yang SESUAI dengan template blade
            $data = $this->fetchData($jenis, $start, $end);
            
            if ($data) {
                $dataGabungan[$jenis] = $data;
            }
        }

        // --- EXPORT ---
        $timestamp = date('d-m-Y_H-i');
        if ($format == 'excel') {
            return Excel::download(new LaporanGabunganExport($dataGabungan), "Laporan_Gabungan_{$timestamp}.xlsx");
        }

        if ($format == 'pdf') {
            $pdf = PDF::loadView('laporan.template-gabungan', compact('dataGabungan'));
            return $pdf->download("Laporan_Gabungan_{$timestamp}.pdf");
        }
    }

    /**
     * DOWNLOAD SATUAN
     */
    public function downloadSatuan(Request $request)
    {
        $rawType = $request->query('type'); 
        $format = $request->query('format');
        $start = $request->query('start');
        $end = $request->query('end');

        // Normalisasi nama
        $type = $this->normalizeType($rawType);
        
        // Ambil Data
        $data = $this->fetchData($type, $start, $end);

        if (!$data) {
            return back()->with('error', "Jenis laporan $type tidak ditemukan atau query salah.");
        }

        // Bungkus data agar struktur array sama dengan gabungan
        $dataWrapper = [
            'tanggalMulai' => $start,
            'tanggalSelesai' => $end,
            $type => $data 
        ];

        $fileName = ucfirst($type) . "_{$start}_sd_{$end}";

        if ($format == 'excel') {
            return Excel::download(new LaporanGabunganExport($dataWrapper), $fileName . '.xlsx');
        }

        if ($format == 'pdf') {
            // Gunakan template gabungan sebagai fallback yang aman
            $pdf = PDF::loadView('laporan.template-gabungan', ['dataGabungan' => $dataWrapper]);
            return $pdf->download($fileName . '.pdf');
        }
    }

    /**
     * HELPER: Normalisasi Nama Frontend ke Nama Database/Key Template
     */
    private function normalizeType($rawValue)
    {
        // Hapus prefix umum
        $clean = str_replace(['laporan_', 'pengelolaan_'], '', $rawValue);

        // Mapping khusus jika nama masih beda
        // Format: 'nama_dari_frontend' => 'key_di_template_blade'
        $mapping = [
            'gangguan_kamtibmas' => 'gangguan',
            'shift_anggota' => 'shift',
        ];

        return $mapping[$clean] ?? $clean;
    }

    /**
     * HELPER: Query Database Terpusat
     */
    private function fetchData($type, $start, $end)
    {
        switch ($type) {
            case 'presensi': 
                return Presensi::whereBetween('tanggal', [$start, $end])->get();
            
            case 'patroli': 
                return Patroli::whereBetween('tanggal', [$start, $end])->get();
            
            case 'tamu': 
                return Tamu::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            
            case 'barang': 
                // GABUNGKAN Barang Temu & Barang Titip
                $temu = BarangTemuan::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
                $titip = BarangTitipan::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
                return $temu->merge($titip); 

            case 'kendaraan': 
                // Gunakan LogKendaraan, bukan Master Kendaraan
                // Filter berdasarkan waktu_masuk
                return LogKendaraan::whereBetween('waktu_masuk', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            
            case 'gangguan': 
                // Filter berdasarkan waktu_lapor
                return GangguanKamtibmas::whereBetween('waktu_lapor', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            
            case 'shift': 
                return Shift::whereBetween('tanggal', [$start, $end])->get();
            
            default: 
                return null;
        }
    }
}