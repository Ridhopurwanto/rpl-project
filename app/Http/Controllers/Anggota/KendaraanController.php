<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\LogKendaraan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KendaraanController extends Controller
{
     
    public function index(Request $request)
    {
        
        $kendaraan_aktif = LogKendaraan::where('status', 'Masuk')
                            ->orderBy('waktu_masuk', 'asc')
                            ->get();

        
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $keterangan_filter = $request->input('keterangan');

        $query = LogKendaraan::where('status', 'Keluar')
                             ->whereDate('waktu_keluar', $tanggal_riwayat);
        
        if ($keterangan_filter) {
            $query->where('keterangan', $keterangan_filter);
        }

        $riwayat_kendaraan = $query->orderBy('waktu_keluar', 'desc')->get();

        return view('anggota.kendaraan-index', [
            'kendaraan_aktif'   => $kendaraan_aktif,
            'riwayat_kendaraan' => $riwayat_kendaraan,
            'tanggal_terpilih'  => $tanggal_riwayat,
            'keterangan_filter' => $keterangan_filter,
        ]);
    }

     
    public function create()
    {
        return view('anggota.kendaraan-create');
    }

     
    public function store(Request $request)
    {
        $request->validate([
            'nopol' => 'required|string|max:20',
            'pemilik' => 'required|string|max:100',
            'tipe' => 'required|string|in:Roda 2,Roda 4',
            'keterangan' => 'required|string|in:Menginap,Tidak Menginap',
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
        ]);

        $nopol = strtoupper($request->nopol);
        
        // Check if vehicle is already inside
        $existingVehicle = LogKendaraan::where('nopol', $nopol)
                                      ->where('status', 'Masuk')
                                      ->first();
        
        if ($existingVehicle) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Kendaraan dengan nomor polisi ' . $nopol . ' sudah berada di dalam kampus. Silakan keluarkan terlebih dahulu sebelum menambahkan kembali.');
        }
        
        $kendaraanMaster = Kendaraan::where('nomor_plat', $nopol)->first();
        $idKendaraan = $kendaraanMaster ? $kendaraanMaster->id_kendaraan : null;
        $waktu_masuk = Carbon::parse($request->tanggal . ' ' . $request->waktu);

        LogKendaraan::create([
            'id_kendaraan' => $idKendaraan, 
            'nopol'        => $nopol, 
            'pemilik'      => $request->pemilik,
            'tipe'         => $request->tipe,
            'keterangan'   => $request->keterangan,
            'waktu_masuk'  => $waktu_masuk,
            'status'       => 'Masuk',
        ]);

        return redirect()->route('anggota.kendaraan.index')
                         ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

     
    public function updateKeterangan(Request $request, $id_kendaraan_log)
    {
        $request->validate([
            'keterangan' => 'required|string|in:Menginap,Tidak Menginap',
        ]);

        $log = LogKendaraan::findOrFail($id_kendaraan_log);

        if ($log->status == 'Masuk') {
            $log->update(['keterangan' => $request->keterangan]);
            return redirect()->route('anggota.kendaraan.index')->with('success', 'Keterangan berhasil diperbarui.');
        }
        
        return redirect()->route('anggota.kendaraan.index')->with('error', 'Tidak dapat mengubah keterangan kendaraan yang sudah keluar.');
    }

     
    public function checkout(Request $request, $id_kendaraan_log)
    {
        $request->validate([
            'menginap' => 'required|boolean',
        ]);

        $log = LogKendaraan::findOrFail($id_kendaraan_log);
        $keterangan = $request->menginap == '1' ? 'Menginap' : 'Tidak Menginap';

        $log->update([
            'waktu_keluar' => Carbon::now(),
            'status'       => 'Keluar',
            'keterangan'   => $keterangan,
        ]);

        return redirect()->route('anggota.kendaraan.index')
                         ->with('success', 'Kendaraan berhasil dikeluarkan.');
    }

     
    public function searchNopol(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:50',
            'tanggal' => 'nullable|date',
        ]);

        $searchTerm = $request->input('search');
        $tanggal = $request->input('tanggal');

        if (empty($searchTerm)) {
            return response()->json([]);
        }

        $searchTerm = strtoupper(trim($searchTerm));

        
        if ($tanggal) {
            $results = LogKendaraan::where('status', 'Keluar')
                                   ->whereDate('waktu_keluar', $tanggal)
                                   ->where(function($query) use ($searchTerm) {
                                       $query->where('nopol', 'LIKE', $searchTerm . '%')
                                             ->orWhereRaw("pemilik REGEXP ?", [
                                                 '(^|[[:space:]])' . preg_quote($searchTerm, '/')
                                             ]);
                                   })
                                   ->select('nopol', 'pemilik', 'tipe')
                                   ->groupBy('nopol', 'pemilik', 'tipe')
                                   ->orderByRaw("CASE 
                                       WHEN nopol LIKE ? THEN 1
                                       WHEN pemilik REGEXP ? THEN 2
                                       ELSE 3
                                   END", [
                                       $searchTerm . '%',
                                       '(^|[[:space:]])' . preg_quote($searchTerm, '/')
                                   ])
                                   ->take(10)
                                   ->get();
            
            return response()->json($results);
        }

        
        $kendaraan = Kendaraan::where(function($query) use ($searchTerm) {
                                    $query->where('nomor_plat', 'LIKE', $searchTerm . '%')
                                          ->orWhereRaw("pemilik REGEXP ?", [
                                              '(^|[[:space:]])' . preg_quote($searchTerm, '/')
                                          ]);
                                })
                                ->select('id_kendaraan', 'nomor_plat', 'pemilik', 'tipe')
                                ->orderByRaw("CASE 
                                    WHEN nomor_plat LIKE ? THEN 1
                                    WHEN pemilik REGEXP ? THEN 2
                                    ELSE 3
                                END", [
                                    $searchTerm . '%',
                                    '(^|[[:space:]])' . preg_quote($searchTerm, '/')
                                ])
                                ->take(10)
                                ->get();

        return response()->json($kendaraan);
    }

     
    public function getRiwayat(Request $request)
    {
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());


        $query = LogKendaraan::where('status', 'Keluar')
                             ->whereDate('waktu_keluar', $tanggal_riwayat);
        
        if ($nopol_filter) {
            $nopol_filter_upper = strtoupper(trim($nopol_filter));
            
            $query->where(function($q) use ($nopol_filter_upper) {
                $q->where('nopol', 'like', $nopol_filter_upper . '%')
                  ->orWhereRaw("pemilik REGEXP ?", [
                      '(^|[[:space:]])' . preg_quote($nopol_filter_upper, '/')
                  ]);
            });
        }

        if ($keterangan_filter) {
            $query->where('keterangan', $keterangan_filter);
        }

        $riwayat_kendaraan = $query->orderBy('waktu_keluar', 'desc')->get();

        
        return view('anggota.kendaraan-riwayat-cards', [
            'riwayat_kendaraan' => $riwayat_kendaraan
        ])->render();
    }
}