<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\BarangTemuan;
use App\Models\BarangTitipan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BarangController extends Controller
{
     
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

        
        $queryTemuan = BarangTemuan::query()
            ->orderByRaw("CASE WHEN status = 'selesai' THEN 0 ELSE 1 END ASC")
            ->orderBy('waktu_lapor', 'desc');
        
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

        
        $queryTitipan = BarangTitipan::query()
            ->orderByRaw("CASE WHEN status = 'selesai' THEN 0 ELSE 1 END ASC")
            ->orderBy('waktu_titip', 'desc');
        
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
            try {
                $htmlTemuan = view('komandan.partials.barang-table-temuan', compact('barangTemuan'))->render();
                $htmlTitipan = view('komandan.partials.barang-table-titipan', compact('barangTitipan'))->render();

                return response()->json([
                    'html_temuan' => $htmlTemuan,
                    'html_titipan' => $htmlTitipan,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
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

    public function updatePenerima(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required|string|max:255',
        ]);

        $barang = BarangTitipan::findOrFail($id);
        
        if ($barang->status == 'selesai') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat mengedit barang yang sudah selesai.']);
            }
            return redirect()->back()->with('error', 'Tidak dapat mengedit barang yang sudah selesai.');
        }

        $barang->update([
            'tujuan' => $request->tujuan
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Nama penerima berhasil diperbarui.']);
        }
        
        return redirect()->back()->with('success', 'Nama penerima berhasil diperbarui.');
    }

    public function updateBarang(Request $request, $type, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_barang' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($type === 'temuan') {
            $request->validate([
                'nama_pelapor' => 'required|string|max:255',
                'lokasi_penemuan' => 'required|string|max:255',
            ]);
            
            $barang = BarangTemuan::findOrFail($id);
            
            if ($barang->status == 'selesai') {
                return response()->json(['success' => false, 'message' => 'Tidak dapat mengedit barang yang sudah selesai.']);
            }

            $barang->update([
                'waktu_lapor' => $request->tanggal,
                'nama_barang' => $request->nama_barang,
                'nama_pelapor' => $request->nama_pelapor,
                'lokasi_penemuan' => $request->lokasi_penemuan,
                'catatan' => $request->catatan,
            ]);
        } else {
            $request->validate([
                'nama_penitip' => 'required|string|max:255',
                'nama_penerima' => 'required|string|max:255',
            ]);
            
            $barang = BarangTitipan::findOrFail($id);
            
            if ($barang->status == 'selesai') {
                return response()->json(['success' => false, 'message' => 'Tidak dapat mengedit barang yang sudah selesai.']);
            }

            $barang->update([
                'waktu_titip' => $request->tanggal,
                'nama_barang' => $request->nama_barang,
                'nama_penitip' => $request->nama_penitip,
                'tujuan' => $request->nama_penerima,
                'catatan' => $request->catatan,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data barang berhasil diperbarui.']);
    }
}