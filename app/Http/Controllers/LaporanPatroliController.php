<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patroli; 
use Illuminate\Support\Facades\Storage; // Untuk menghapus foto

class LaporanPatroliController extends Controller
{

    public function index(Request $request)
    {
        // Ambil tanggal dari filter, jika tidak ada, gunakan tanggal hari ini
        $tanggalTerpilih = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Ambil jenis patroli dari filter
        $jenisPatroliTerpilih = $request->input('jenis_patroli', 'semua');

        // Mulai query
        $query = Patroli::query();

        // Filter berdasarkan tanggal
        $query->whereDate('tanggal', $tanggalTerpilih);
        
        if ($jenisPatroliTerpilih != 'semua') {
             $query->where('jenis_patroli', $jenisPatroliTerpilih);
        }

        // Ambil data dan urutkan berdasarkan waktu
        $dataPatroli = $query->orderBy('waktu_exact', 'asc')->get();

        // Kirim data ke view
        return view('laporan.patroli', [
            'dataPatroli' => $dataPatroli,
            'tanggalTerpilih' => $tanggalTerpilih,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
        ]);
    }

    /**
     * Mengupdate data wilayah patroli (dari modal edit).
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'wilayah' => 'required|string|max:255',
        ]);

        try {
            $patroli = Patroli::findOrFail($id);
            $patroli->wilayah = $request->wilayah;
            $patroli->save();

            return back()->with('success', 'Data patroli berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data patroli.
     */
    public function destroy($id)
    {
        try {
            $patroli = Patroli::findOrFail($id);
            
            // Hapus foto dari storage sebelum menghapus data
            if ($patroli->foto) {
                Storage::delete('public/' . $patroli->foto);
            }
            
            $patroli->delete();

            return back()->with('success', 'Data patroli berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}