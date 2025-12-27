<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\GangguanKamtibmas; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class GangguanKamtibmasController extends Controller
{
     
    public function index(Request $request)
    {
        
        $bulanFilter = $request->input('bulan', now()->format('Y-m'));
        $carbonDate = Carbon::createFromFormat('Y-m', $bulanFilter);

        
        $kategoriFilter = $request->input('kategori');
        
        $perPage = $request->input('per_page', 10);

        
        $query = GangguanKamtibmas::query()
                    ->whereYear('waktu_lapor', $carbonDate->year)
                    ->whereMonth('waktu_lapor', $carbonDate->month);

        
        if ($kategoriFilter && $kategoriFilter != 'semua') {
            $query->where('kategori', $kategoriFilter);
        }

        $riwayatGangguan = $query->orderBy('waktu_lapor', 'desc')->paginate($perPage);

        
        try {
            $result = \DB::select("SHOW COLUMNS FROM gangguan_kamtibmas WHERE Field = 'kategori'");
            $enumStr = $result[0]->Type;
            preg_match("/^enum\((.+)\)$/", $enumStr, $matches);
            $kategoriOptions = str_getcsv($matches[1], ',', "'");
        } catch (\Exception $e) {
            
            $kategoriOptions = ['Curat', 'Curas', 'Curanmor', 'Narkoba', 'Laka Lantas', 'Pembunuhan', 'Perkelahian', 'Mabok', 'Unjuk Rasa', 'Penyerobotan Tanah', 'Kenakalan Remaja', 'Kebakaran', 'Bencana Alam'];
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('komandan.partials.gangguan-list', [
                    'riwayatGangguan' => $riwayatGangguan,
                ])->render(),
            ]);
        }

        return view('komandan.gangguan', [
            'riwayatGangguan' => $riwayatGangguan,
            'bulanTerpilih' => $bulanFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'kategoriOptions' => $kategoriOptions,
            'perPage' => $perPage,
        ]);
    }

     
    public function update(Request $request, $id_gangguan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.gangguan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        
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

            
            $params = [
                'bulan' => $request->input('bulan'),
                'per_page' => $request->input('per_page'),
                'kategori' => $request->input('kategori_filter'), 
            ];

            return redirect()->route('komandan.gangguan', $params)
                             ->with('success', 'Laporan gangguan berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui laporan.');
        }
    }

     
    public function destroy($id_gangguan)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.gangguan')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $gangguan = GangguanKamtibmas::findOrFail($id_gangguan);
            
            
            if ($gangguan->foto) {
                Storage::disk('public')->delete($gangguan->foto);
            }

            
            $gangguan->delete();
            
            
            
            
            $params = [
                'bulan' => request('bulan'),
                'per_page' => request('per_page'),
                'kategori' => request('kategori_filter'),
            ];
            
            return redirect()->route('komandan.gangguan', $params)
                             ->with('success', 'Laporan gangguan berhasil dihapus.');
        
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus laporan.');
        }
    }
}