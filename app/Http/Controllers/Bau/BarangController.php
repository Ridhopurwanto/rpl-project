<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangTemuan;
use App\Models\BarangTitipan;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman Laporan Barang (Read Only untuk BAU).
     */
    public function index(Request $request)
    {
        $tanggalTemuan = $request->input('tanggal_temuan', now()->format('Y-m-d'));
        $tanggalTitipan = $request->input('tanggal_titipan', now()->format('Y-m-d'));
        $searchTemuan = $request->input('search_temuan');
        $searchTitipan = $request->input('search_titipan');
        $perPageTemuan = $request->input('per_page_temuan', 10);
        $perPageTitipan = $request->input('per_page_titipan', 10);

        $queryTemuan = BarangTemuan::whereDate('waktu_lapor', $tanggalTemuan);
        if ($searchTemuan) {
            $queryTemuan->where(function($q) use ($searchTemuan) {
                $q->where('nama_barang', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('nama_pelapor', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('lokasi_penemuan', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('catatan', 'LIKE', "%{$searchTemuan}%");
            });
        }
        $barangTemuan = $queryTemuan->orderBy('waktu_lapor', 'desc')->paginate($perPageTemuan, ['*'], 'page_temuan');

        $queryTitipan = BarangTitipan::whereDate('waktu_titip', $tanggalTitipan);
        if ($searchTitipan) {
            $queryTitipan->where(function($q) use ($searchTitipan) {
                $q->where('nama_barang', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('nama_penitip', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('tujuan', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('catatan', 'LIKE', "%{$searchTitipan}%");
            });
        }
        $barangTitipan = $queryTitipan->orderBy('waktu_titip', 'desc')->paginate($perPageTitipan, ['*'], 'page_titipan');

        return view('bau.barang', [
            'barangTemuan' => $barangTemuan,
            'barangTitipan' => $barangTitipan,
            'tanggalTemuan' => $tanggalTemuan,
            'tanggalTitipan' => $tanggalTitipan,
            'searchTemuan' => $searchTemuan,
            'searchTitipan' => $searchTitipan,
            'perPageTemuan' => $perPageTemuan,
            'perPageTitipan' => $perPageTitipan,
        ]);
    }
}
