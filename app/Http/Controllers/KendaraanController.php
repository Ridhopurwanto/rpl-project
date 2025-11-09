<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\LogKendaraan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class KendaraanController extends Controller
{
    /**
     * Menampilkan halaman utama laporan kendaraan (Riwayat & Master)
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 48]
     */
    public function index(Request $request)
    {
        // --- Filter untuk RIWAYAT ---
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $tipeFilter = $request->input('tipe'); // 'Roda 2' atau 'Roda 4'

        $queryRiwayat = LogKendaraan::with('kendaraan', 'pengguna')
                            ->whereDate('tanggal', $tanggalFilter);

        if ($tipeFilter) {
            $queryRiwayat->whereHas('kendaraan', function ($q) use ($tipeFilter) {
                $q->where('tipe', $tipeFilter);
            });
        }
        
        $riwayat = $queryRiwayat->orderBy('waktu_masuk', 'desc')->get();

        // --- Data untuk KENDARAAN TERDAFTAR ---
        $kendaraanMaster = Kendaraan::orderBy('pemilik', 'asc')->get();

        $registeredPlates = $kendaraanMaster->pluck('nomor_plat')->toArray();

        // --- PERBAIKAN: Mengarah ke folder 'komandan' ---
        return view('komandan.kendaraan', [
            'riwayat' => $riwayat,
            'kendaraanMaster' => $kendaraanMaster,
            'tanggalTerpilih' => $tanggalFilter,
            'tipeTerpilih' => $tipeFilter,
            'registeredPlates' => $registeredPlates, // <-- Kirim data plat ke view
        ]);
    }

    // --- FUNGSI CRUD UNTUK KENDARAAN MASTER ---

    /**
     * Menampilkan form edit Kendaraan Master
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 48]
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
     * Disesuaikan dengan kendaraan.sql (tanpa 'keterangan') [cite: kendaraan.sql]
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
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 48]
     */
    public function destroyMaster($id_kendaraan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.kendaraan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            // Cek jika kendaraan masih punya log, cegah hapus
            $adaLog = LogKendaraan::where('id_kendaraan', $id_kendaraan)->exists();
            if ($adaLog) {
                return redirect()->back()->with('error', 'Gagal menghapus! Kendaraan masih memiliki riwayat log.');
            }

            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            $kendaraan->delete();
            return redirect()->back()->with('success', 'Data kendaraan master berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Menghapus data dari Log Kendaraan (Riwayat)
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 48]
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
}