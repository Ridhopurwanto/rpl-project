<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\ShiftRule;

class PresensiController extends Controller
{
    /**
     * Menampilkan halaman laporan presensi (Read Only untuk BAU).
     */
    public function index(Request $request)
    {
        // Ambil filter tanggal, default: hari ini.
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Ambil filter shift, default: 'semua'.
        $shiftFilter = $request->input('shift', 'semua');

        // Query dasar: Gabungkan Presensi dengan Shift
        $query = Presensi::join('shift', 'presensi.id_shift', '=', 'shift.id_shift')
                         ->whereDate('presensi.tanggal', $tanggalFilter)
                         ->select('presensi.*', 'shift.jenis_shift');   

        $shiftId = ShiftRule::where('jenis_shift', $shiftFilter)
                                  ->first();

        // Terapkan filter shift jika bukan 'semua'
        if ($shiftFilter !== 'semua') {
            $query->where('shift.jenis_shift', $shiftId->idshift_rule);
        }

        // Clone query dasar untuk memisahkan Masuk dan Pulang
        $queryMasuk = clone $query;
        $queryPulang = clone $query;

        // Ambil data PRESENSI MASUK
        $dataMasuk = $queryMasuk->where('presensi.jenis_presensi', 'Masuk')
                               ->orderBy('presensi.waktu', 'asc')
                               ->get();

        // Ambil data PRESENSI PULANG
        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->get();

        // Ambil data Shift Rule (meski BAU tidak edit, data ini mungkin dipakai di view untuk info)
        $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Non Shift'])->get();
        
        $globalRule = $rules->firstWhere('jenis_shift', 'Pagi');

        return view('bau.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter,
            'rules' => $rules,
            'globalToleransi' => $globalRule ? $globalRule->toleransi : 0,
            'globalDibuka' => $globalRule ? $globalRule->dibuka : 0,
        ]);
    }
}
