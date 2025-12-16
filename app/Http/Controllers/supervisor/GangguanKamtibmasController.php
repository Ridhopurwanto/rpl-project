<?php

namespace App\Http\Controllers\Supervisor;

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
        
        $perPage = $request->input('per_page', 10);

        // Query dasar
        $query = GangguanKamtibmas::query()
                    ->whereYear('waktu_lapor', $carbonDate->year)
                    ->whereMonth('waktu_lapor', $carbonDate->month);

        // Terapkan filter kategori jika ada (dan bukan 'semua')
        if ($kategoriFilter && $kategoriFilter != 'semua') {
            $query->where('kategori', $kategoriFilter);
        }

        $riwayatGangguan = $query->orderBy('waktu_lapor', 'desc')->paginate($perPage);

        // Ambil daftar Kategori dari ENUM di database
        try {
            $result = \DB::select("SHOW COLUMNS FROM gangguan_kamtibmas WHERE Field = 'kategori'");
            $enumStr = $result[0]->Type;
            preg_match("/^enum\((.+)\)$/", $enumStr, $matches);
            $kategoriOptions = str_getcsv($matches[1], ',', "'");
        } catch (\Exception $e) {
            // Fallback jika query gagal
            $kategoriOptions = ['Curat', 'Curas', 'Curanmor', 'Narkoba', 'Laka Lantas', 'Pembunuhan', 'Perkelahian', 'Mabok', 'Unjuk Rasa', 'Penyerobotan Tanah', 'Kenakalan Remaja', 'Kebakaran', 'Bencana Alam'];
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('supervisor.partials.gangguan-list', [
                    'riwayatGangguan' => $riwayatGangguan,
                ])->render(),
            ]);
        }

        return view('supervisor.gangguan', [
            'riwayatGangguan' => $riwayatGangguan,
            'bulanTerpilih' => $bulanFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'kategoriOptions' => $kategoriOptions,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Update data gangguan (HANYA UNTUK SUPERVISOR).
     *
     */
    public function update(Request $request, $id_gangguan)
    {
        if (Auth::user()->peran !== 'supervisor') {
            return redirect()->route('bau.gangguan.index')->with('error', 'Anda tidak memiliki hak akses.');
        }

        // Ambil kategori dari ENUM untuk validasi
        try {
            $result = \DB::select("SHOW COLUMNS FROM gangguan_kamtibmas WHERE Field = 'kategori'");
            $enumStr = $result[0]->Type;
            preg_match("/^enum\((.+)\)$/", $enumStr, $matches);
            $kategoriOptions = str_getcsv($matches[1], ',', "'");
        } catch (\Exception $e) {
            $kategoriOptions = ['Curat', 'Curas', 'Curanmor', 'Narkoba', 'Laka Lantas', 'Pembunuhan', 'Perkelahian', 'Mabok', 'Unjuk Rasa', 'Penyerobotan Tanah', 'Kenakalan Remaja', 'Kebakaran', 'Bencana Alam'];
        }

        $request->validate([
            'waktu_lapor' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'kategori' => 'required|in:' . implode(',', $kategoriOptions),
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
     * Menghapus data gangguan (HANYA UNTUK SUPERVISOR).
     *
     */
    public function destroy($id_gangguan)
    {
        if (Auth::user()->peran !== 'supervisor') {
            return redirect()->route('bau.gangguan.index')->with('error', 'Anda tidak memiliki hak akses.');
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