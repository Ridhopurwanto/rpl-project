<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GangguanKamtibmas;
use Illuminate\Support\Carbon;

class GangguanController extends Controller
{
    /**
     * Menampilkan halaman Laporan Gangguan Kamtibmas (Read Only untuk BAU).
     */
    public function index(Request $request)
    {
        // Filter Bulan
        $bulanFilter = $request->input('bulan', now()->format('Y-m'));
        $carbonDate = Carbon::createFromFormat('Y-m', $bulanFilter);

        // Filter Kategori
        $kategoriFilter = $request->input('kategori');

        // Query dasar
        $query = GangguanKamtibmas::query()
                    ->whereYear('waktu_lapor', $carbonDate->year)
                    ->whereMonth('waktu_lapor', $carbonDate->month);

        if ($kategoriFilter && $kategoriFilter != 'semua') {
            $query->where('kategori', $kategoriFilter);
        }

        $riwayatGangguan = $query->orderBy('waktu_lapor', 'desc')->get();

        $kategoriOptions = ['Unjuk Rasa', 'Pembakaran Lahan', 'Bentrokan Kepolisian', 'Kriminalitas', 'Kecelakaan', 'Lainnya'];

        return view('bau.gangguan', [
            'riwayatGangguan' => $riwayatGangguan,
            'bulanTerpilih' => $bulanFilter,
            'kategoriTerpilih' => $kategoriFilter,
            'kategoriOptions' => $kategoriOptions,
        ]);
    }
}
