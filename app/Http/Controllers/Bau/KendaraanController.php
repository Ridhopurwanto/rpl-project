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
        // 1. Ambil Filter
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); 
        $search = $request->input('search');

        // 2. Query Dasar Riwayat (Filter Tanggal)
        $queryRiwayat = LogKendaraan::with('kendaraan')
            ->where(function($q) use ($tanggalFilter) {
                $q->whereDate('waktu_masuk', $tanggalFilter)
                  ->orWhereDate('waktu_keluar', $tanggalFilter);
            });

        // 3. Filter Tipe (Jika ada)
        if ($tipeFilter) {
            $queryRiwayat->whereHas('kendaraan', function ($q) use ($tipeFilter) {
                $q->where('tipe', $tipeFilter);
            });
        }

        // 4. Search
        if ($search) {
            $queryRiwayat->where(function($q) use ($search) {
                $q->where('nopol', 'LIKE', "%{$search}%")
                  ->orWhere('pemilik', 'LIKE', "%{$search}%");
            });
        }
        
        // Eksekusi Query Riwayat
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->get();

        // --- Data untuk KENDARAAN TERDAFTAR ---
        $kendaraanMaster = Kendaraan::orderBy('pemilik', 'asc')->get();
        $registeredPlates = $kendaraanMaster->pluck('nomor_plat')->toArray();

        return view('bau.kendaraan', [
            'riwayat' => $riwayat,
            'kendaraanMaster' => $kendaraanMaster,
            'tanggalTerpilih' => $tanggalFilter,
            'tipeTerpilih' => $tipeFilter,
            'registeredPlates' => $registeredPlates,
            'search' => $search
        ]);
    }
}
