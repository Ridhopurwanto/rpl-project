<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Patroli;
use App\Models\Shift;
use App\Models\Tamu;       
use App\Models\GangguanKamtibmas;
use App\Models\BarangTemuan; 
use App\Models\BarangTitipan;
use App\Models\LogKendaraan;
use App\Models\User;
use App\Models\Kendaraan;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\LaporanGabunganExport; 
use Maatwebsite\Excel\Facades\Excel;

class LaporanUnduhController extends Controller
{
    public function index()
    {
        return view('komandan.unduh');
    }

    public function download(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $queue  = json_decode($request->download_queue, true);
        $format = $request->format;

        if (!$queue || count($queue) === 0) {
            return back()->with('error', 'Tidak ada laporan dipilih');
        }

        
        $minStart = null;
        $maxEnd   = null;

        foreach ($queue as $q) {
            $s = $q['dateStart'];
            $e = $q['dateEnd'];

            if (is_null($minStart) || $s < $minStart) $minStart = $s;
            if (is_null($maxEnd)   || $e > $maxEnd)   $maxEnd   = $e;
        }

        $dataGabungan = [
            'tanggalMulai'   => $minStart ?? date('Y-m-d'),
            'tanggalSelesai' => $maxEnd   ?? date('Y-m-d'),
        ];

        foreach ($queue as $item) {
            $jenis = $this->normalizeType($item['value']);
            $start = $item['dateStart'];
            $end   = $item['dateEnd'];

            $data = $this->fetchData($jenis, $start, $end);
            
            if ($data) {
                $dataGabungan[$jenis] = $data;
            }
        }

        $timestamp = date('d-m-Y_H-i');
        
        if ($format == 'excel') {
            return Excel::download(
                new LaporanGabunganExport($dataGabungan),
                "Laporan Gabungan {$timestamp}.xlsx"
            );
        }

        if ($format == 'pdf') {
            
            $hasShift = isset($dataGabungan['shift']);
            $orientation = $hasShift ? 'landscape' : 'portrait';

            $pdf = Pdf::loadView('komandan.laporan.template-pdf', $dataGabungan)
                ->setPaper('a4', $orientation)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            
            return $pdf->download("Laporan Gabungan {$timestamp}.pdf");
        }

        
        return back()->with('error', 'Format tidak valid');
    }

    public function downloadSatuan(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $rawType = $request->query('type'); 
        $format  = $request->query('format');
        $start   = $request->query('start');
        $end     = $request->query('end');

        $type = $this->normalizeType($rawType);

        
        if ($type == 'barang') {
            $dataTemu = $this->fetchData('barang_temu', $start, $end);
            $dataTitip = $this->fetchData('barang_titip', $start, $end);
            
            
            if ($dataTemu->isEmpty() && $dataTitip->isEmpty()) {
                 return back()->with('error', "Laporan barang tidak ditemukan pada periode tersebut.");
            }

            $dataWrapper = [
                'tanggalMulai'   => $start,
                'tanggalSelesai' => $end,
                'barang_temu'    => $dataTemu,
                'barang_titip'   => $dataTitip,
            ];
        } else {
            
            $data = $this->fetchData($type, $start, $end);

            if (!$data) {
                return back()->with('error', "Jenis laporan $type tidak ditemukan.");
            }

            $dataWrapper = [
                'tanggalMulai'   => $start,
                'tanggalSelesai' => $end,
                $type            => $data,
            ];
        }

        $fileName = ucfirst(str_replace('_', ' ', $type)) . " {$start} sd {$end}";

        if ($format == 'excel') {
            return Excel::download(
                new LaporanGabunganExport($dataWrapper),
                $fileName . '.xlsx'
            );
        }
        
        if ($format == 'pdf') {
            
            $orientation = ($type == 'shift') ? 'landscape' : 'portrait';

            $pdf = Pdf::loadView('komandan.laporan.template-pdf', $dataWrapper)
                ->setPaper('a4', $orientation)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            
            return $pdf->download($fileName . '.pdf');
        }

        return back()->with('error', 'Format tidak valid');
    }

    private function normalizeType($rawValue)
    {
        $clean = str_replace(['laporan_', 'pengelolaan_'], '', $rawValue);
        $mapping = [
            'gangguan_kamtibmas' => 'gangguan',
            'shift_anggota'      => 'shift',
            'barang_temu'        => 'barang_temu',
            'barang_titip'       => 'barang_titip',
            'anggota'            => 'anggota',
            'kendaraan_terdaftar'=> 'kendaraan_terdaftar',
        ];
        return $mapping[$clean] ?? $clean;
    }

    private function fetchData($type, $start, $end)
    {
        $startFull = $start . ' 00:00:00';
        $endFull   = $end   . ' 23:59:59';

        switch ($type) {
            case 'presensi':    
                return Presensi::with('pengguna')
                               ->whereBetween('tanggal', [$start, $end])
                               ->orderBy('tanggal', 'asc')
                               ->orderBy('waktu', 'asc')
                               ->get();
            case 'patroli':     
                return Patroli::with(['claim.rule'])
                              ->whereBetween('tanggal', [$start, $end])
                              ->orderBy('tanggal', 'asc')
                              ->orderBy('waktu_exact', 'asc')
                              ->get();
            case 'tamu':        
                return Tamu::whereDate('waktu_datang', '>=', $start)
                           ->whereDate('waktu_datang', '<=', $end)
                           ->orderBy('waktu_datang', 'asc')
                           ->get();
            case 'barang_temu': 
                return BarangTemuan::whereDate('waktu_lapor', '>=', $start)
                                   ->whereDate('waktu_lapor', '<=', $end)
                                   ->orderBy('waktu_lapor', 'asc')
                                   ->get();
            case 'barang_titip':
                return BarangTitipan::whereDate('waktu_titip', '>=', $start)
                                    ->whereDate('waktu_titip', '<=', $end)
                                    ->orderBy('waktu_titip', 'asc')
                                    ->get();
            case 'kendaraan':   
                return LogKendaraan::whereBetween('waktu_masuk', [$startFull, $endFull])
                                   ->orderBy('waktu_masuk', 'asc')
                                   ->get();
            case 'gangguan':    
                return GangguanKamtibmas::whereBetween('waktu_lapor', [$startFull, $endFull])
                                        ->orderBy('waktu_lapor', 'asc')
                                        ->get();
            case 'shift':       
                
                $users = User::whereIn('peran', ['anggota', 'komandan', 'supervisor']) 
                             ->orderBy('nama_lengkap', 'asc')
                             ->get();
                
                
                $shifts = Shift::with(['shiftRule'])
                               ->whereBetween('tanggal', [$start, $end])
                               ->get();
                
                
                foreach ($users as $user) {
                    
                    $userShifts = $shifts->where('id_pengguna', $user->id_pengguna);
                    
                    
                    $shiftMap = [];
                    foreach ($userShifts as $s) {
                        $shiftMap[$s->tanggal] = $s;
                    }
                    $user->shifts_by_date = $shiftMap;
                }
                
                return $users;
            case 'anggota':     
                return User::whereIn('peran', ['anggota', 'komandan'])
                           ->orderBy('nama_lengkap', 'asc') 
                           ->get();
            case 'kendaraan_terdaftar': return Kendaraan::orderBy('tipe', 'asc')->get();
            default:            return null;
        }
    }
}
