<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tamu; 

class TamuController extends Controller
{
    /**
     * Menampilkan halaman Laporan Tamu (Read Only untuk BAU).
     */
    public function index(Request $request)
    {
        // Ambil tanggal dari filter. Default: hari ini.
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Ambil data tamu berdasarkan filter tanggal
        $riwayatTamu = Tamu::whereDate('waktu_datang', '>=', $startDate)
                           ->whereDate('waktu_datang', '<=', $endDate)
                           ->orderBy('waktu_datang', 'asc')
                           ->get();
        
        return view('bau.tamu', [
            'riwayatTamu' => $riwayatTamu,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
