<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\LogKendaraan; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class KendaraanController extends Controller
{
     
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
            'html' => view('komandan.partials.riwayat-table', compact('riwayat', 'tanggalFilter', 'registeredPlates'))->render(),
            'pagination' => view('komandan.partials.riwayat-pagination', compact('riwayat'))->render()
        ]);
    }

     
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

     
    public function index(Request $request)
    {
        
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); 
        $search = $request->input('search'); 

        
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
        
        
        $perPageRiwayat = $request->input('per_page_riwayat', 10);
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->paginate($perPageRiwayat, ['*'], 'page_riwayat');

        
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

    

     
    public function editMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            
            
            return view('komandan.kendaraan_edit', ['kendaraan' => $kendaraan]);
        
        } catch (\Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }
    }

     
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
            
            return redirect()->back()->with('success', 'Data kendaraan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

     
    public function destroyMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);

            
            
            
            
            LogKendaraan::where('id_kendaraan', $id_kendaraan)
                        ->update(['id_kendaraan' => null]);

            
            $kendaraan->delete();
            
            return redirect()->back()->with('success', 'Data kendaraan berhasil dihapus dari Daftar Kendaraan (Riwayat tetap tersimpan).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

     
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

    
    


    
    
     
    public function updateKeterangan(Request $request, $id_log)
    {
        
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        
        $request->validate([
            'keterangan' => 'required|string|in:Menginap,Tidak Menginap',
        ]);

        try {
            
            $log = LogKendaraan::findOrFail($id_log);
            
            
            $log->update([
                'keterangan' => $request->keterangan
            ]);
            
            
            return redirect()->back()->with('success', 'Status keterangan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'GGagal memperbarui keterangan.');
        }
    }
    
     
    public function promoteLogToMaster($id_log)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $log = LogKendaraan::findOrFail($id_log);

            
            if ($log->id_kendaraan) {
                return redirect()->back()->with('error', 'Kendaraan ini sudah ada di master.');
            }
            
            
            $existingMaster = Kendaraan::where('nomor_plat', $log->nopol)->first();

            if ($existingMaster) {
                
                $log->update(['id_kendaraan' => $existingMaster->id_kendaraan]);
                return redirect()->back()->with('success', 'Kendaraan sudah ada di master. Log telah ditautkan.');
            }

            
            $kendaraanMaster = Kendaraan::create([
                'nomor_plat' => $log->nopol,
                'pemilik'    => $log->pemilik,
                'tipe'       => $log->tipe,
            ]);

            
            $log->update([
                'id_kendaraan' => $kendaraanMaster->id_kendaraan
            ]);

            return redirect()->back()->with('success', 'Kendaraan berhasil ditambahkan ke Daftar Master.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mempromosikan kendaraan: ' . $e->getMessage());
        }
    }
}