<?php

namespace App\Http\Controllers\Supervisor;

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

    
}