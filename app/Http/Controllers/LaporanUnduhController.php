<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Patroli;
use App\Models\Shift;
use App\Models\Tamu;       
use App\Models\GangguanKamtibmas;
use App\Models\BarangTemuan; 
use App\Models\BarangTitipan;
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

    public function download(Request $request)
    {
        $queue = json_decode($request->download_queue, true);
        $format = $request->format;

        if (!$queue || count($queue) === 0) {
            return back()->with('error', 'Tidak ada laporan dipilih');
        }

        // Data Dasar
        $dataGabungan = [
            'tanggalMulai' => $queue[0]['dateStart'] ?? date('Y-m-d'),
            'tanggalSelesai' => $queue[0]['dateEnd'] ?? date('Y-m-d'),
        ];

        foreach ($queue as $item) {
            $jenis = $this->normalizeType($item['value']);
            $start = $item['dateStart'];
            $end = $item['dateEnd'];

            $data = $this->fetchData($jenis, $start, $end);
            
            if ($data) {
                // Data disimpan sesuai key (presensi, patroli, barang_temu, dll)
                $dataGabungan[$jenis] = $data;
            }
        }

        $timestamp = date('d-m-Y_H-i');
        
        if ($format == 'excel') {
            // Panggil LaporanGabunganExport yang baru (Multi-Sheet)
            return Excel::download(new LaporanGabunganExport($dataGabungan), "Laporan_Gabungan_{$timestamp}.xlsx");
        }

        if ($format == 'pdf') {
            // PDF tidak support multi-sheet (tab), jadi kita render semua dalam satu file panjang
            // Kita bisa menggunakan View yang sama, tapi perlu sedikit modifikasi jika mau rapi.
            // Untuk sekarang, kita fallback render semua jenis sheet secara berurutan.
            // Namun, karena template kita sekarang 'per-sheet', kita harus render loop di PDF.
            // Ini akan butuh view blade khusus 'template-pdf-all' yang meng-include template-excel berulang kali.
            
            // SEMENTARA: Gunakan metode manual render view untuk PDF
            // PDF biasanya memanjang ke bawah, jadi logic ini agak beda dengan Excel Sheet.
            // Tapi untuk code ini, saya fokuskan Excel dulu sesuai permintaan "Multi Sheet".
            // PDF akan render blank jika logic blade tidak di-loop.
            
            // Solusi Cepat PDF: Render ulang template dengan mode 'all' atau loop di Controller
            // Untuk simplisitas, saya biarkan dulu PDF menggunakan template lama atau perlu file baru khusus PDF.
            
            // Agar PDF jalan dengan struktur baru, kita harus buat file view wrapper
            return back()->with('error', 'Fitur PDF Multi-Sheet sedang dalam pengembangan. Gunakan Excel.');
        }
    }

    public function downloadSatuan(Request $request)
    {
        $rawType = $request->query('type'); 
        $format = $request->query('format');
        $start = $request->query('start');
        $end = $request->query('end');

        $type = $this->normalizeType($rawType);
        $data = $this->fetchData($type, $start, $end);

        if (!$data) {
            return back()->with('error', "Jenis laporan $type tidak ditemukan.");
        }

        // Bungkus agar struktur array bisa dibaca oleh LaporanGabunganExport
        $dataWrapper = [
            'tanggalMulai' => $start,
            'tanggalSelesai' => $end,
            $type => $data 
        ];

        $fileName = ucfirst($type) . "_{$start}_sd_{$end}";

        if ($format == 'excel') {
            // Satuan juga akan jadi Excel dengan 1 Sheet
            return Excel::download(new LaporanGabunganExport($dataWrapper), $fileName . '.xlsx');
        }
        
        // ... PDF logic ...
    }

    private function normalizeType($rawValue)
    {
        $clean = str_replace(['laporan_', 'pengelolaan_'], '', $rawValue);
        $mapping = [
            'gangguan_kamtibmas' => 'gangguan',
            'shift_anggota' => 'shift',
            'barang_temu' => 'barang_temu',
            'barang_titip' => 'barang_titip',
        ];
        return $mapping[$clean] ?? $clean;
    }

    private function fetchData($type, $start, $end)
    {
        $startFull = $start . ' 00:00:00';
        $endFull = $end . ' 23:59:59';

        switch ($type) {
            case 'presensi': return Presensi::whereBetween('tanggal', [$start, $end])->get();
            case 'patroli': return Patroli::whereBetween('tanggal', [$start, $end])->get();
            case 'tamu': return Tamu::whereBetween('created_at', [$startFull, $endFull])->get();
            case 'barang_temu': return BarangTemuan::whereBetween('created_at', [$startFull, $endFull])->get();
            case 'barang_titip': return BarangTitipan::whereBetween('created_at', [$startFull, $endFull])->get();
            case 'kendaraan': return LogKendaraan::whereBetween('waktu_masuk', [$startFull, $endFull])->get();
            case 'gangguan': return GangguanKamtibmas::whereBetween('waktu_lapor', [$startFull, $endFull])->get();
            case 'shift': return Shift::whereBetween('tanggal', [$start, $end])->get();
            default: return null;
        }
    }
}