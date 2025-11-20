<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tamu; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TamuController extends Controller
{
    /**
     * Menampilkan halaman Laporan Tamu (untuk Komandan dan BAU).
     * Menggunakan nama 'index' sesuai permintaan.
     */
    public function index(Request $request)
    {
        // Ambil tanggal dari filter. Default: hari ini.
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        // Ambil data tamu berdasarkan filter tanggal
        $riwayatTamu = Tamu::whereDate('waktu_datang', '>=', $startDate)
                           ->whereDate('waktu_datang', '<=', $endDate)
                           ->orderBy('waktu_datang', 'asc')
                           ->get();
        
        return view('komandan.tamu', [
            'riwayatTamu' => $riwayatTamu,
            'startDate' => $startDate, // Kirim balik ke view agar input tetap terisi
            'endDate' => $endDate,     // Kirim balik ke view agar input tetap terisi
        ]);
    }

    /**
     * Update data tamu (HANYA UNTUK KOMANDAN).
     */
    public function update(Request $request, $id_tamu)
    {
        // Pengecekan keamanan: Hanya Komandan
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.tamu')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'waktu_datang' => 'required|date',
        ]);

        try {
            $tamu = Tamu::findOrFail($id_tamu);
            
            $tamu->update([
                'nama_tamu' => $request->nama_tamu,
                'instansi' => $request->instansi,
                'tujuan' => $request->tujuan,
                'waktu_datang' => $request->waktu_datang,
            ]);

            return redirect()->back()->with('success', 'Data tamu berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Menghapus data tamu (HANYA UNTUK KOMANDAN).
     */
    public function destroy($id_tamu)
    {
        // Pengecekan keamanan: Hanya Komandan
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.tamu')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $tamu = Tamu::findOrFail($id_tamu);
            $tamu->delete();
            
            return redirect()->back()->with('success', 'Data tamu berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}