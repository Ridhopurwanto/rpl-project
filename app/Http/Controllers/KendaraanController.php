<?php

namespace App\Http\Controllers;

// 'use App\Http\Controllers\KendaraanController;' DIHAPUS
use Illuminate\Http\Request;
use App\Models\Kendaraan; 
use App\Models\LogKendaraan; // Sesuai file Anda
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception; 

// PERBAIKAN: 'Controllers' diubah menjadi 'Controller'
class KendaraanController extends Controller 
{
    /**
     * Menampilkan halaman laporan kendaraan (master dan log).
     * Rute: komandan.kendaraan
     */
    public function index()
    {
        try {
            // Ambil data master kendaraan
            $kendaraans = Kendaraan::orderBy('nomor_plat', 'asc')->get();
            
            // Ambil data log pengecekan
            $logs = LogKendaraan::with('kendaraan', 'pengguna') // Asumsi ada relasi
                                ->orderBy('waktu_pengecekan', 'desc')
                                ->get();

            return view('komandan.kendaraan', [
                'kendaraans' => $kendaraans,
                'logs' => $logs
            ]);

        } catch (Exception $e) {
            // Jika tabel tidak ada atau error query
            return redirect()->back()->with('error', 'Gagal memuat data kendaraan: ' . $e->getMessage());
        }
    }

    /**
     * [DEPRECATED dengan Modal]
     * Menampilkan halaman edit master (rute ini ada di web.php).
     * Rute: komandan.kendaraan.master.edit
     */
    public function editMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            // Rute ini tidak akan terpakai jika modal berhasil,
            // tapi kita biarkan sesuai web.php
            return view('komandan.kendaraan_edit', ['kendaraan' => $kendaraan]);

        } catch (Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Data kendaraan tidak ditemukan.');
        }
    }


    /**
     * Mengupdate data master kendaraan (dari modal).
     * Rute: komandan.kendaraan.master.update
     */
    public function updateMaster(Request $request, $id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'required|string|max:255',
            'nomor_plat' => 'required|string|max:20',
            'status' => 'required|string|in:Tersedia,Digunakan,Perbaikan',
        ]);

        if ($validator->fails()) {
            return redirect()->route('komandan.kendaraan')
                             ->withErrors($validator)
                             ->with('error', 'Validasi gagal. Pastikan semua field terisi.');
        }

        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            
            $kendaraan->update([
                'nama_kendaraan' => $request->nama_kendaraan,
                'nomor_plat' => $request->nomor_plat,
                'status' => $request->status,
            ]);

            return redirect()->route('komandan.kendaraan')->with('success', 'Data kendaraan berhasil diperbarui.');

        } catch (Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Gagal memperbarui data kendaraan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data master kendaraan.
     * Rute: komandan.kendaraan.master.destroy
     */
    public function destroyMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }
        
        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            
            if ($kendaraan->logs()->count() > 0) {
                 return redirect()->route('komandan.kendaraan')->with('error', 'Gagal hapus: Kendaraan ini memiliki riwayat log pengecekan.');
            }
            
            $kendaraan->delete();
            
            return redirect()->route('komandan.kendaraan')->with('success', 'Data kendaraan berhasil dihapus.');
        
        } catch (Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    /**
     * Mengupdate keterangan pada log pengecekan kendaraan.
     * Rute: komandan.kendaraan.log.updateKeterangan
     */
    public function updateKeterangan(Request $request, $id_log)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $request->validate([
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            $log = LogKendaraan::findOrFail($id_log);
            $log->update([
                'keterangan' => $request->keterangan,
            ]);
            
            return redirect()->route('komandan.kendaraan')->with('success', 'Keterangan log berhasil diperbarui.');

        } catch (Exception $e) {
            return redirect()->route('komandan.kendaraan')->with('error', 'Gagal memperbarui keterangan log.');
        }
    }
}