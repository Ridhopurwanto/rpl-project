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
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        $shiftFilter = $request->input('shift', 'semua');
        $perPage = $request->input('per_page', 10);

        $query = Presensi::join('shift', 'presensi.id_shift', '=', 'shift.id_shift')
                         ->whereDate('presensi.tanggal', $tanggalFilter)
                         ->select('presensi.*', 'shift.jenis_shift');

        if ($shiftFilter !== 'semua') {
            $query->where('shift.jenis_shift', $shiftFilter);
        }

        $queryMasuk = clone $query;
        $queryPulang = clone $query;

        $dataMasuk = $queryMasuk->where('presensi.jenis_presensi', 'Masuk')
                               ->orderBy('presensi.waktu', 'asc')
                               ->paginate($perPage, ['*'], 'page_masuk');

        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->paginate($perPage, ['*'], 'page_pulang');

        return view('bau.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter,
            'perPage' => $perPage,
        ]);
    }
}
