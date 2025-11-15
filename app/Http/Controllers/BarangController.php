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
     * [cite: 2360, 2439]
     */
    public function index(Request $request)
    {
        // Ambil filter dari request
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Kategori: 'temuan' atau 'titipan'
        $kategoriFilter = $request->input('kategori', 'temuan'); 
        
        // Jenis: Filter berdasarkan nama_barang
        $jenisFilter = $request->input('jenis'); 

        $query = null;
        
        // Logika untuk membedakan query berdasarkan Kategori
        if ($kategoriFilter == 'temuan') {
            $query = BarangTemuan::query()
                        ->whereDate('waktu_lapor', $tanggalFilter)
                        ->orderBy('waktu_lapor', 'desc');
            
        } else { // Kategori 'titipan'
            $query = BarangTitipan::query()
                        ->whereDate('waktu_titip', $tanggalFilter)
                        ->orderBy('waktu_titip', 'desc');
        }

        // Terapkan filter Jenis (nama_barang) jika ada
        if ($jenisFilter) {
            $query->where('nama_barang', 'like', '%' . $jenisFilter . '%');
        }

        $riwayatBarang = $query->get();

        return view('komandan.barang', [
            'riwayatBarang' => $riwayatBarang,
            'tanggalTerpilih' => $tanggalFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'jenisTerpilih' => $jenisFilter,
        ]);
    }

    // Fungsi edit/delete tidak ditambahkan sesuai deskripsi UI/UX
    // yang menyatakan read-only untuk Komandan [cite: 2360, 2363, 2367]
}