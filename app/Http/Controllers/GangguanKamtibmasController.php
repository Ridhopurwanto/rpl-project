<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GangguanKamtibmas; // Panggil Model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class GangguanKamtibmasController extends Controller
{
    /**
     * Menampilkan halaman Laporan Gangguan Kamtibmas (Komandan & BAU).
     *
     */
    public function index(Request $request)
    {
        // Filter Bulan: Ambil 'YYYY-MM' dari request, default bulan ini
        $bulanFilter = $request->input('bulan', now()->format('Y-m'));
        $carbonDate = Carbon::createFromFormat('Y-m', $bulanFilter);

        // Filter Kategori
        $kategoriFilter = $request->input('kategori');

        // Query dasar
        $query = GangguanKamtibmas::query()
                    ->whereYear('waktu_lapor', $carbonDate->year)
                    ->whereMonth('waktu_lapor', $carbonDate->month);

        // Terapkan filter kategori jika ada (dan bukan 'semua')
        if ($kategoriFilter && $kategoriFilter != 'semua') {
            $query->where('kategori', $kategoriFilter);
        }

        $riwayatGangguan = $query->orderBy('waktu_lapor', 'desc')->get();

        // Ambil daftar Kategori dari ENUM di database
        //
        $kategoriOptions = ['Unjuk Rasa', 'Pembakaran Lahan', 'Bentrokan Kepolisian', 'Kriminalitas', 'Kecelakaan', 'Lainnya'];

        return view('komandan.gangguan', [
            'riwayatGangguan' => $riwayatGangguan,
            'bulanTerpilih' => $bulanFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'kategoriOptions' => $kategoriOptions,
        ]);
    }

    /**
     * Update data gangguan (HANYA UNTUK KOMANDAN).
     *
     */
    public function update(Request $request, $id_gangguan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.gangguan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $request->validate([
            'waktu_lapor' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'kategori' => 'required|string',
            'deskripsi' => 'required|string',
        ]);

        try {
            $gangguan = GangguanKamtibmas::findOrFail($id_gangguan);
            
            $gangguan->update([
                'waktu_lapor' => $request->waktu_lapor,
                'lokasi' => $request->lokasi,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->back()->with('success', 'Laporan gangguan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui laporan.');
        }
    }

    /**
     * Menghapus data gangguan (HANYA UNTUK KOMANDAN).
     *
     */
    public function destroy($id_gangguan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.gangguan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $gangguan = GangguanKamtibmas::findOrFail($id_gangguan);
            
            // Hapus foto dari storage
            if ($gangguan->foto) {
                Storage::disk('public')->delete($gangguan->foto);
            }

            // Hapus data dari database
            $gangguan->delete();
            
            return redirect()->back()->with('success', 'Laporan gangguan berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus laporan.');
        }
    }
}