<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangTemuan;
use App\Models\BarangTitipan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman Laporan Barang (untuk Komandan dan BAU).
     */
    public function index(Request $request)
    {
        // 1. Ambil filter dari request
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Kategori: 'temuan' atau 'titipan'
        $kategoriFilter = $request->input('kategori', 'temuan'); 
        
        // Search: Ganti 'jenis' jadi 'search' agar lebih umum
        $search = $request->input('search'); 

        $query = null;
        
        // 2. Inisialisasi Query dasar berdasarkan Kategori & Tanggal
        if ($kategoriFilter == 'temuan') {
            $query = BarangTemuan::query()
                        ->whereDate('waktu_lapor', $tanggalFilter)
                        ->orderBy('waktu_lapor', 'desc');
            
        } else { // Kategori 'titipan'
            $query = BarangTitipan::query()
                        ->whereDate('waktu_titip', $tanggalFilter)
                        ->orderBy('waktu_titip', 'desc');
        }

        // 3. Logika LIVE SEARCH Multi-Kolom
        if ($search) {
            $query->where(function($q) use ($search, $kategoriFilter) {
                // Pencarian Umum (Ada di kedua tabel)
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('catatan', 'LIKE', "%{$search}%");

                // Pencarian Spesifik per Kategori
                if ($kategoriFilter == 'temuan') {
                    // Cari Pelapor & Lokasi (khusus Temuan)
                    $q->orWhere('nama_pelapor', 'LIKE', "%{$search}%")
                      ->orWhere('lokasi_penemuan', 'LIKE', "%{$search}%");
                } else {
                    // Cari Penitip & Tujuan (khusus Titipan)
                    $q->orWhere('nama_penitip', 'LIKE', "%{$search}%")
                      ->orWhere('tujuan', 'LIKE', "%{$search}%");
                }
            });
        }

        $riwayatBarang = $query->get();

        return view('komandan.barang', [
            'riwayatBarang' => $riwayatBarang,
            'tanggalTerpilih' => $tanggalFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'jenisTerpilih' => $search, // Oper variabel search ke view (bisa dipakai buat value input)
        ]);
    }
}