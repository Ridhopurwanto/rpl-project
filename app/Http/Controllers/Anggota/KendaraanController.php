<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\LogKendaraan;
use App\Models\Kendaraan; // <-- TAMBAHKAN INI
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());

        // 3. Ambil riwayat kendaraan yang "Keluar"
        $riwayat_kendaraan = LogKendaraan::where('status', 'Keluar')
                            ->whereDate('waktu_keluar', $tanggal_riwayat)
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
     * Menyimpan data kendaraan baru dari form.
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

        // 2. LOGIKA BARU: Update atau Buat di tabel master 'kendaraans'
        // Ini mendaftarkan kendaraan di master list secara otomatis
        $kendaraan = Kendaraan::updateOrCreate(
            ['nomor_plat' => $nopol], // Kunci pencarian
            [ // Data untuk update/create
                'pemilik' => $request->pemilik,
                'tipe' => $request->tipe
            ]
        );

        // 3. Gabungkan tanggal dan waktu
        $waktu_masuk = Carbon::parse($request->tanggal . ' ' . $request->waktu);

        // 4. Buat Log Kendaraan (Sesuai alur Anda)
        LogKendaraan::create([
            'id_kendaraan' => $kendaraan->id, // Link ke master
            'nopol' => $nopol, // Duplikat data nopol
            'pemilik' => $request->pemilik, // Duplikat data pemilik
            'tipe' => $request->tipe, // Duplikat data tipe
            'keterangan' => $request->keterangan, // Simpan Menginap/Tidak
            'waktu_masuk' => $waktu_masuk,
            'status' => 'Masuk',
        ]);

        return redirect()->route('anggota.kendaraan.index')
                         ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * BARU: Meng-update Keterangan (Menginap/Tidak) dari halaman index.
     */
    public function updateKeterangan(Request $request, $id_kendaraan_log)
    {
        $request->validate([
            'keterangan' => 'required|string|in:Menginap,Tidak Menginap',
        ]);

        $log = LogKendaraan::findOrFail($id_kendaraan_log);

        // Pastikan hanya update jika status masih "Masuk"
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
        // (Logika ini kembali seperti semula, hanya update status & waktu)
        
        $log = LogKendaraan::findOrFail($id_kendaraan_log);

        // Update data kendaraan
        $log->update([
            'waktu_keluar' => Carbon::now(),
            'status' => 'Keluar',
        ]);

        return redirect()->route('anggota.kendaraan.index')
                         ->with('success', 'Kendaraan berhasil dikeluarkan.');
    }

    public function searchNopol(Request $request)
    {
        $request->validate(['search' => 'nullable|string|max:20']);

        $searchTerm = $request->input('search');

        if (empty($searchTerm)) {
            return response()->json([]);
        }

        // Cari di tabel master 'kendaraan' berdasarkan 'nomor_plat'
        $kendaraan = Kendaraan::where('nomor_plat', 'LIKE', $searchTerm . '%')
                              ->take(5) // Ambil 5 hasil teratas
                              ->get();

        // Kembalikan sebagai JSON
        return response()->json($kendaraan);
    }
}