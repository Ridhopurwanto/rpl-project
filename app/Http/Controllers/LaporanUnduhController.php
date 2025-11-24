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
                $dataGabungan[$jenis] = $data;
            }
        }

        $timestamp = date('d-m-Y_H-i');
        if ($format == 'excel') {
            return Excel::download(new LaporanGabunganExport($dataGabungan), "Laporan_Gabungan_{$timestamp}.xlsx");
        }

        if ($format == 'pdf') {
            $pdf = PDF::loadView('laporan.template-gabungan', compact('dataGabungan'));
            return $pdf->download("Laporan_Gabungan_{$timestamp}.pdf");
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
            return back()->with('error', "Jenis laporan $type tidak ditemukan atau query salah.");
        }

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
            $pdf = PDF::loadView('laporan.template-gabungan', ['dataGabungan' => $dataWrapper]);
            return $pdf->download($fileName . '.pdf');
        }
    }

    private function normalizeType($rawValue)
    {
        $clean = str_replace(['laporan_', 'pengelolaan_'], '', $rawValue);
        $mapping = [
            'gangguan_kamtibmas' => 'gangguan',
            'shift_anggota' => 'shift',
            // Pastikan nama ini sesuai dengan value di checkbox frontend
            'barang_temu' => 'barang_temu',
            'barang_titip' => 'barang_titip',
        ];
        return $mapping[$clean] ?? $clean;
    }

    private function fetchData($type, $start, $end)
    {
        // Format Filter Tanggal
        // Gunakan Filter Full Day (00:00 s/d 23:59)
        $startFull = $start . ' 00:00:00';
        $endFull = $end . ' 23:59:59';

        switch ($type) {
            case 'presensi': 
                return Presensi::whereBetween('tanggal', [$start, $end])->get();
            
            case 'patroli': 
                return Patroli::whereBetween('tanggal', [$start, $end])->get();
            
            case 'tamu': 
                return Tamu::whereBetween('created_at', [$startFull, $endFull])->get();
            
            case 'barang_temu': 
                // Query Khusus Barang Temuan
                return BarangTemuan::whereBetween('created_at', [$startFull, $endFull])->get();

            case 'barang_titip': 
                // Query Khusus Barang Titipan
                return BarangTitipan::whereBetween('created_at', [$startFull, $endFull])->get();

            case 'kendaraan': 
                return LogKendaraan::whereBetween('waktu_masuk', [$startFull, $endFull])->get();
            
            case 'gangguan': 
                return GangguanKamtibmas::whereBetween('waktu_lapor', [$startFull, $endFull])->get();
            
            case 'shift': 
                return Shift::whereBetween('tanggal', [$start, $end])->get();
            
            default: 
                return null;
        }
    }
}