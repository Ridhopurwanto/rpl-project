<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GangguanKamtibmas;
use Illuminate\Support\Carbon;

class GangguanController extends Controller
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
        $kategoriOptions = ['Unjuk Rasa', 'Pembakaran Lahan', 'Bentrokan Kepolisian', 'Kriminalitas', 'Kecelakaan', 'Lainnya'];

        return view('bau.gangguan', [
            'riwayatGangguan' => $riwayatGangguan,
            'bulanTerpilih' => $bulanFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'kategoriOptions' => $kategoriOptions,
            'perPage' => $perPage,
        ]);
    }
}
