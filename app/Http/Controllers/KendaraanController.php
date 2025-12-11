<?php

namespace App\Http\Controllers;

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
        $registeredPlates = Kendaraan::pluck('nomor_plat')->toArray();

        return response()->json([
            'html' => view('komandan.partials.riwayat-table', compact('riwayat', 'tanggalFilter', 'registeredPlates'))->render(),
            'pagination' => view('komandan.partials.riwayat-pagination', compact('riwayat'))->render()
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
            'html' => view('komandan.partials.master-table', compact('kendaraanMaster'))->render(),
            'pagination' => view('komandan.partials.master-pagination', compact('kendaraanMaster'))->render()
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
                  ->orWhereDate('waktu_keluar', $tanggalFilter);
            });

        // 3. Filter Tipe (Jika ada)
        if ($tipeFilter) {
            $queryRiwayat->whereHas('kendaraan', function ($q) use ($tipeFilter) {
                $q->where('tipe', $tipeFilter);
            });
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

        return view('komandan.kendaraan', [
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

    // --- FUNGSI CRUD UNTUK KENDARAAN MASTER ---

    /**
     * Menampilkan form edit Kendaraan Master
     *
     */
    public function editMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            
            // --- PERBAIKAN: Mengarah ke folder 'komandan' ---
            return view('komandan.kendaraan_edit', ['kendaraan' => $kendaraan]);
        
        } catch (\Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }
    }

    /**
     * Menyimpan update Kendaraan Master
     * Disesuaikan dengan kendaraan.sql (tanpa 'keterangan')
     */
    public function updateMaster(Request $request, $id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $request->validate([
            'nomor_plat' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'tipe' => 'required|in:Roda 2,Roda 4',
        ]);

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            $kendaraan->update($request->only('nomor_plat', 'pemilik', 'tipe'));
            
            return redirect()->route('komandan.kendaraan')->with('success', 'Data kendaraan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Menghapus data dari Kendaraan Master
     *
     */
    public function destroyMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);

            // LANGKAH PENTING:
            // Sebelum menghapus Master, kita harus "memutuskan hubungan" dengan Log-nya dulu.
            // Kita update semua Log yang punya id_kendaraan ini menjadi NULL.
            // Jadi datanya tetap ada di riwayat (sebagai teks nopol/pemilik), tapi tidak lagi terikat ke ID Master ini.
            LogKendaraan::where('id_kendaraan', $id_kendaraan)
                        ->update(['id_kendaraan' => null]);

            // Setelah log-nya "dilepas", baru kita hapus Masternya dengan aman.
            $kendaraan->delete();
            
            return redirect()->back()->with('success', 'Data kendaraan berhasil dihapus dari Daftar Kendaraan (Riwayat tetap tersimpan).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Menghapus data dari Log Kendaraan (Riwayat)
     *
     */
    public function destroyLog($id_log)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $log = LogKendaraan::findOrFail($id_log);
            $log->delete();
            return redirect()->back()->with('success', 'Data riwayat kendaraan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data riwayat.');
        }
    }

    // --- FUNGSI UNTUK ANGGOTA ---
    // (Nanti ditambahkan di sini)


    // ▼▼▼ INI FUNGSI YANG HILANG & SAYA TAMBAHKAN ▼▼▼
    
    /**
     * Mengupdate keterangan (menginap/tidak) dari tabel riwayat.
     *
     */
    public function updateKeterangan(Request $request, $id_log)
    {
        // Keamanan: Hanya komandan yang boleh
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        // Validasi input
        $request->validate([
            'keterangan' => 'required|string|in:menginap,tidak menginap',
        ]);

        try {
            // Cari log berdasarkan ID
            $log = LogKendaraan::findOrFail($id_log);
            
            // Update data
            $log->update([
                'keterangan' => $request->keterangan
            ]);
            
            // Kembali ke halaman sebelumnya (halaman filter)
            return redirect()->back()->with('success', 'Status keterangan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'GGagal memperbarui keterangan.');
        }
    }
    
    /**
     * "Mempromosikan" data dari log ke tabel master.
     * Ini dipanggil oleh tombol (+) di tabel Riwayat.
     */
    public function promoteLogToMaster($id_log)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $log = LogKendaraan::findOrFail($id_log);

            // 1. Cek apakah sudah dipromosikan (id_kendaraan sudah terisi)
            if ($log->id_kendaraan) {
                return redirect()->back()->with('error', 'Kendaraan ini sudah ada di master.');
            }
            
            // 2. Cek apakah plat nomornya sudah ada (kasus log lain dipromote duluan)
            $existingMaster = Kendaraan::where('nomor_plat', $log->nopol)->first();

            if ($existingMaster) {
                // Jika sudah ada, cukup update log-nya
                $log->update(['id_kendaraan' => $existingMaster->id_kendaraan]);
                return redirect()->back()->with('success', 'Kendaraan sudah ada di master. Log telah ditautkan.');
            }

            // 3. Buat data baru di tabel master 'kendaraan'
            $kendaraanMaster = Kendaraan::create([
                'nomor_plat' => $log->nopol,
                'pemilik'    => $log->pemilik,
                'tipe'       => $log->tipe,
            ]);

            // 4. Tautkan log ini ke master yang baru dibuat
            $log->update([
                'id_kendaraan' => $kendaraanMaster->id_kendaraan
            ]);

            return redirect()->back()->with('success', 'Kendaraan berhasil ditambahkan ke Daftar Master.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mempromosikan kendaraan: ' . $e->getMessage());
        }
    }
}