<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\LogKendaraan;

class KendaraanController extends Controller
{
    /**
     * Menampilkan halaman utama laporan kendaraan (Riwayat & Master) - Read Only
     */
    public function index(Request $request)
    {
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); 
        $search = $request->input('search');
        $tipeMaster = $request->input('tipe_master');
        $searchMaster = $request->input('search_master');
        $perPageRiwayat = $request->input('per_page_riwayat', 10);
        $perPageMaster = $request->input('per_page_master', 10);

        $queryRiwayat = LogKendaraan::with('kendaraan')
            ->where(function($q) use ($tanggalFilter) {
                $q->whereDate('waktu_masuk', $tanggalFilter)
                  ->orWhereDate('waktu_keluar', $tanggalFilter);
            });

        if ($tipeFilter) {
            $queryRiwayat->whereHas('kendaraan', function ($q) use ($tipeFilter) {
                $q->where('tipe', $tipeFilter);
            });
        }

        if ($search) {
            $queryRiwayat->where(function($q) use ($search) {
                $q->where('nopol', 'LIKE', "%{$search}%")
                  ->orWhere('pemilik', 'LIKE', "%{$search}%");
            });
        }
        
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->paginate($perPageRiwayat, ['*'], 'page_riwayat');
        
        $queryMaster = Kendaraan::query();
        
        if ($tipeMaster) {
            $queryMaster->where('tipe', $tipeMaster);
        }
        
        if ($searchMaster) {
            $queryMaster->where(function($q) use ($searchMaster) {
                $q->where('nomor_plat', 'LIKE', "%{$searchMaster}%")
                  ->orWhere('pemilik', 'LIKE', "%{$searchMaster}%");
            });
        }
        
        $kendaraanMaster = $queryMaster->orderBy('pemilik', 'asc')->paginate($perPageMaster, ['*'], 'page_master');

        return view('bau.kendaraan', [
            'riwayat' => $riwayat,
            'kendaraanMaster' => $kendaraanMaster,
            'tanggalTerpilih' => $tanggalFilter,
            'tipeTerpilih' => $tipeFilter,
            'search' => $search,
            'tipeMaster' => $tipeMaster,
            'searchMaster' => $searchMaster,
            'perPageRiwayat' => $perPageRiwayat,
            'perPageMaster' => $perPageMaster,
        ]);
    }
}
