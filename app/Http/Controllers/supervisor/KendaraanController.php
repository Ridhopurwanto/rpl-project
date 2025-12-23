<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\LogKendaraan; // Pastikan ini ada
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class KendaraanController extends Controller
{
    /**
     * AJAX Live Search untuk Riwayat
     */
    public function searchRiwayat(Request $request)
    {
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); 
        $search = $request->input('search');
        $perPageRiwayat = $request->input('per_page_riwayat', 10);

        $queryRiwayat = LogKendaraan::with('kendaraan')
            ->where(function($q) use ($tanggalFilter) {
                $q->whereDate('waktu_masuk', $tanggalFilter)
                  ->orWhereDate('waktu_keluar', $tanggalFilter)
                  ->orWhere('status', 'Masuk');
            });

        if ($tipeFilter) {
            $queryRiwayat->where('tipe', $tipeFilter);
        }

        if ($search) {
            $queryRiwayat->where(function($q) use ($search) {
                $q->where('nopol', 'LIKE', "%{$search}%")
                  ->orWhere('pemilik', 'LIKE', "%{$search}%");
            });
        }
        
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->paginate($perPageRiwayat, ['*'], 'page_riwayat');
        $registeredPlates = Kendaraan::pluck('nomor_plat')->toArray();

        return response()->json([
            'html' => view('supervisor.partials.riwayat-table', compact('riwayat', 'tanggalFilter', 'registeredPlates'))->render(),
            'pagination' => view('supervisor.partials.riwayat-pagination', compact('riwayat'))->render()
        ]);
    }

    /**
     * AJAX Live Search untuk Master
     */
    public function searchMaster(Request $request)
    {
        $tipeMaster = $request->input('tipe_master');
        $searchMaster = $request->input('search_master');
        $perPageMaster = $request->input('per_page_master', 10);
        
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

        return response()->json([
            'html' => view('supervisor.partials.master-table', compact('kendaraanMaster'))->render(),
            'pagination' => view('supervisor.partials.master-pagination', compact('kendaraanMaster'))->render()
        ]);
    }

    /**
     * Menampilkan halaman utama laporan kendaraan (Riwayat & Master)
     *
     */
    public function index(Request $request)
    {
        // 1. Ambil Filter
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); 
        $search = $request->input('search'); // <--- TAMBAHAN: Input Search

        // 2. Query Dasar Riwayat (Filter Tanggal)
        $queryRiwayat = LogKendaraan::with('kendaraan')
            ->where(function($q) use ($tanggalFilter) {
                $q->whereDate('waktu_masuk', $tanggalFilter)
                  ->orWhereDate('waktu_keluar', $tanggalFilter)
                  ->orWhere('status', 'Masuk');
            });

        // 3. Filter Tipe (Jika ada) - Cek tipe dari log langsung, bukan dari relasi kendaraan
        if ($tipeFilter) {
            $queryRiwayat->where('tipe', $tipeFilter);
        }

        // ▼▼▼ 4. LOGIKA LIVE SEARCH (KHUSUS RIWAYAT) ▼▼▼
        if ($search) {
            $queryRiwayat->where(function($q) use ($search) {
                $q->where('nopol', 'LIKE', "%{$search}%")     // Cari Plat (ABCD...)
                  ->orWhere('pemilik', 'LIKE', "%{$search}%"); // Cari Pemilik
            });
        }
        
        // Eksekusi Query Riwayat dengan Pagination
        $perPageRiwayat = $request->input('per_page_riwayat', 10);
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->paginate($perPageRiwayat, ['*'], 'page_riwayat');

        // --- Data untuk KENDARAAN TERDAFTAR dengan Pagination ---
        $perPageMaster = $request->input('per_page_master', 10);
        $tipeMaster = $request->input('tipe_master');
        $searchMaster = $request->input('search_master');
        
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
        $registeredPlates = $kendaraanMaster->pluck('nomor_plat')->toArray();

        return view('supervisor.kendaraan', [
            'riwayat' => $riwayat,
            'kendaraanMaster' => $kendaraanMaster,
            'tanggalTerpilih' => $tanggalFilter,
            'tipeTerpilih' => $tipeFilter,
            'registeredPlates' => $registeredPlates,
            'search' => $search,
            'tipeMaster' => $tipeMaster,
            'searchMaster' => $searchMaster,
            'perPageRiwayat' => $perPageRiwayat,
            'perPageMaster' => $perPageMaster
        ]);
    }


}