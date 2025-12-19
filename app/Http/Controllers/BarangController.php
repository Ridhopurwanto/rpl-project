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
        $tanggalTemuan = $request->input('tanggal_temuan', now()->format('Y-m-d'));
        $tanggalTitipan = $request->input('tanggal_titipan', now()->format('Y-m-d'));
        $searchTemuan = $request->input('search_temuan');
        $searchTitipan = $request->input('search_titipan');
        $statusTemuan = $request->input('status_temuan');
        $statusTitipan = $request->input('status_titipan');
        $perPageTemuan = $request->input('per_page_temuan', 10);
        $perPageTitipan = $request->input('per_page_titipan', 10);

        // Query Barang Temuan
        $queryTemuan = BarangTemuan::query()->orderBy('waktu_lapor', 'desc');
        
        if ($tanggalTemuan) {
            $queryTemuan->where(function($q) use ($tanggalTemuan) {
                $q->whereDate('waktu_lapor', '<=', $tanggalTemuan)
                  ->where(function($sub) use ($tanggalTemuan) {
                      $sub->whereNull('waktu_selesai')
                          ->orWhereDate('waktu_selesai', '>=', $tanggalTemuan);
                  });
            });
        }
        
        if ($statusTemuan) {
            $queryTemuan->where('status', $statusTemuan);
        }
        
        if ($searchTemuan) {
            $queryTemuan->where(function($q) use ($searchTemuan) {
                $q->where('nama_barang', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('catatan', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('nama_pelapor', 'LIKE', "%{$searchTemuan}%")
                  ->orWhere('lokasi_penemuan', 'LIKE', "%{$searchTemuan}%");
            });
        }

        // Query Barang Titipan
        $queryTitipan = BarangTitipan::query()->orderBy('waktu_titip', 'desc');
        
        if ($tanggalTitipan) {
            $queryTitipan->where(function($q) use ($tanggalTitipan) {
                $q->whereDate('waktu_titip', '<=', $tanggalTitipan)
                  ->where(function($sub) use ($tanggalTitipan) {
                      $sub->whereNull('waktu_selesai')
                          ->orWhereDate('waktu_selesai', '>=', $tanggalTitipan);
                  });
            });
        }
        
        if ($statusTitipan) {
            $queryTitipan->where('status', $statusTitipan);
        }
        
        if ($searchTitipan) {
            $queryTitipan->where(function($q) use ($searchTitipan) {
                $q->where('nama_barang', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('catatan', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('nama_penitip', 'LIKE', "%{$searchTitipan}%")
                  ->orWhere('tujuan', 'LIKE', "%{$searchTitipan}%");
            });
        }

        $barangTemuan = $queryTemuan->paginate($perPageTemuan, ['*'], 'page_temuan');
        $barangTitipan = $queryTitipan->paginate($perPageTitipan, ['*'], 'page_titipan');

        if ($request->ajax()) {
            return view('komandan.partials.barang-list', compact(
                'barangTemuan', 
                'barangTitipan', 
                'tanggalTemuan', 
                'tanggalTitipan', 
                'searchTemuan', 
                'searchTitipan', 
                'statusTemuan',
                'statusTitipan',
                'perPageTemuan', 
                'perPageTitipan'
            ));
        }

        return view('komandan.barang', [
            'barangTemuan' => $barangTemuan,
            'barangTitipan' => $barangTitipan,
            'tanggalTemuan' => $tanggalTemuan,
            'tanggalTitipan' => $tanggalTitipan,
            'searchTemuan' => $searchTemuan,
            'searchTitipan' => $searchTitipan,
            'statusTemuan' => $statusTemuan,
            'statusTitipan' => $statusTitipan,
            'perPageTemuan' => $perPageTemuan,
            'perPageTitipan' => $perPageTitipan,
        ]);
    }
}