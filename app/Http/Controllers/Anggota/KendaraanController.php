<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\LogKendaraan;
use App\Models\Kendaraan; // Pastikan ini di-import
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // <-- Tetap di sini untuk nanti

class KendaraanController extends Controller
{
    /**
     * Menampilkan halaman index (daftar kendaraan aktif & riwayat).
     */
    public function index(Request $request)
    {
        // 1. Ambil kendaraan yang statusnya "Masuk"
        $kendaraan_aktif = LogKendaraan::where('status', 'Masuk')
                            ->orderBy('waktu_masuk', 'asc')
                            ->get();

        // 2. Filter tanggal riwayat
        // PERBAIKAN: Filter berdasarkan 'waktu_keluar' karena kolom 'tanggal' tidak ada
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());

        // 3. Ambil riwayat kendaraan yang "Keluar"
        $riwayat_kendaraan = LogKendaraan::where('status', 'Keluar')
                            ->whereDate('waktu_keluar', $tanggal_riwayat) // <-- Diubah ke waktu_keluar
                            ->orderBy('waktu_keluar', 'desc')
                            ->get();

        return view('anggota.kendaraan-index', [
            'kendaraan_aktif' => $kendaraan_aktif,
            'riwayat_kendaraan' => $riwayat_kendaraan,
            'tanggal_terpilih' => $tanggal_riwayat,
        ]);
    }

    /**
     * Menampilkan halaman form tambah kendaraan.
     */
    public function create()
    {
        return view('anggota.kendaraan-create');
    }

    /**
     * =========================================================
     * PERBAIKAN FUNGSI STORE
     * Menghapus 'id_pengguna' dan 'tanggal' agar sesuai DB
     * =========================================================
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nopol' => 'required|string|max:20',
            'pemilik' => 'required|string|max:100',
            'tipe' => 'required|string|in:Roda 2,Roda 4',
            'keterangan' => 'required|string|in:Menginap,Tidak Menginap',
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
        ]);

        $nopol = strtoupper($request->nopol);

        // 2. LOGIKA BARU:
        // Cukup CARI kendaraan di tabel master. JANGAN buat baru.
        $kendaraanMaster = Kendaraan::where('nomor_plat', $nopol)->first();
        
        // Dapatkan ID-nya jika ada, jika tidak, biarkan NULL
        $idKendaraan = $kendaraanMaster ? $kendaraanMaster->id_kendaraan : null;

        // 3. Gabungkan tanggal dan waktu
        $waktu_masuk = Carbon::parse($request->tanggal . ' ' . $request->waktu);

        // 4. Buat Log Kendaraan
        LogKendaraan::create([
            'id_kendaraan' => $idKendaraan, 
            // 'id_pengguna' DIHAPUS
            'nopol'        => $nopol, 
            'pemilik'      => $request->pemilik,
            'tipe'         => $request->tipe,
            'keterangan'   => $request->keterangan,
            'waktu_masuk'  => $waktu_masuk,
            'status'       => 'Masuk',
            // 'tanggal' DIHAPUS
        ]);

        return redirect()->route('anggota.kendaraan.index')
                         ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Meng-update Keterangan (Menginap/Tidak) dari halaman index.
     */
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


    /**
     * Memproses checkout kendaraan (dari modal "Keluar").
     */
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

    /**
     * API untuk fitur autocomplete di form create
     */
    public function searchNopol(Request $request)
    {
        $request->validate(['search' => 'nullable|string|max:20']);
        $searchTerm = $request->input('search');

        if (empty($searchTerm)) {
            return response()->json([]);
        }

        $kendaraan = Kendaraan::where('nomor_plat', 'LIKE', $searchTerm . '%')
                              ->take(5)
                              ->get();

        return response()->json($kendaraan);
    }
}