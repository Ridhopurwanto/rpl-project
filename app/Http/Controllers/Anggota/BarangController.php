<?php
// app/Http/Controllers/Anggota/BarangController.php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\BarangTitipan; // Model baru
use App\Models\BarangTemuan;  // Model baru
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str; 

class BarangController extends Controller
{
    /**
     * Menampilkan halaman index (3 tabel)
     */
    public function index(Request $request)
    {
        // 1. Barang Titipan Aktif
        $barang_titipan = BarangTitipan::where('status', 'belum selesai')
                                ->orderBy('waktu_titip', 'desc')
                                ->get();
        
        // 2. Barang Temuan Aktif
        $barang_temuan = BarangTemuan::where('status', 'belum selesai')
                               ->orderBy('waktu_lapor', 'desc')
                               ->get();

        // 3. Riwayat (Logika Gabungan)
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_riwayat = $request->input('kategori_riwayat', 'semua');

        $riwayat_titipan = collect();
        $riwayat_temuan = collect();

        if ($kategori_riwayat == 'semua' || $kategori_riwayat == 'titip') {
            $riwayat_titipan = BarangTitipan::where('status', 'selesai')
                                ->whereDate('waktu_selesai', $tanggal_riwayat)
                                ->get();
        }

        if ($kategori_riwayat == 'semua' || $kategori_riwayat == 'temu') {
            $riwayat_temuan = BarangTemuan::where('status', 'selesai')
                                ->whereDate('waktu_selesai', $tanggal_riwayat)
                                ->get();
        }

        // Gabungkan 2 collection dan urutkan
        $riwayat_barang = $riwayat_titipan
                            ->merge($riwayat_temuan)
                            ->sortByDesc('waktu_selesai');

        return view('anggota.barang-index', [
            'barang_titipan' => $barang_titipan,
            'barang_temuan' => $barang_temuan,
            'riwayat_barang' => $riwayat_barang,
            'tanggal_terpilih' => $tanggal_riwayat,
            'kategori_terpilih' => $kategori_riwayat,
        ]);
    }

    /**
     * Menampilkan form tambah barang
     */
    public function create()
    {
        return view('anggota.barang-create');
    }

    /**
     * Menyimpan barang baru ke tabel yang benar
     */
    public function store(Request $request)
    {
        // Validasi data umum
        $request->validate([
            'kategori' => 'required|in:titipan,temuan',
            'nama_barang' => 'required|string|max:255',
            'nama_pelapor' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|string', // <-- PERUBAHAN 1: Validasi sebagai string (Base64)
            // 'tanggal' => 'nullable|date', // Tambahkan ini jika Anda tetap menggunakan field tanggal
        ]);

        // --- PERUBAHAN 2: Handle Base64 ---
        $pathFoto = null;
        // 'foto' adalah nama input hidden dari view
        if ($request->filled('foto')) { 
            try {
                // Pisahkan data Base64
                list($type, $data) = explode(';', $request->foto);
                list(, $data)      = explode(',', $data);
                $imageData = base64_decode($data);
                
                // Buat nama file unik
                $filename = 'foto_barang/' . Str::random(20) . '.jpg';
                
                // Simpan file ke disk 'public'
                Storage::disk('public')->put($filename, $imageData);
                $pathFoto = $filename; // Simpan path-nya

            } catch (\Exception $e) {
                // Tangani error jika Base64 tidak valid
                return back()->with('error', 'Format foto tidak valid. Silakan ambil ulang foto.');
            }
        }
        // --- AKHIR PERUBAHAN 2 ---

        // Logika pemecah (Tetap sama)
        if ($request->kategori == 'titip') {
            
            // Validasi khusus titipan
            $data = $request->validate([
                'tujuan' => 'required|string|max:255',
            ]);

            // Simpan ke BarangTitipan
            BarangTitipan::create([
                'id_pengguna' => Auth::id(),
                'nama_barang' => $request->nama_barang,
                'nama_penitip' => $request->nama_pelapor, // 'nama_pelapor' dari form
                'tujuan' => $data['tujuan'],
                'foto' => $pathFoto, // Path foto yang sudah disimpan
                'catatan' => $request->catatan,
                'waktu_titip' => Carbon::now(), // Gunakan Carbon::now()
                'status' => 'belum selesai',
            ]);

        } else { // 'temu'

            // Validasi khusus temuan
            $data = $request->validate([
                'lokasi_penemuan' => 'required|string|max:255',
            ]);

            // Simpan ke BarangTemuan
            BarangTemuan::create([
                'id_pengguna' => Auth::id(),
                'nama_barang' => $request->nama_barang,
                'nama_pelapor' => $request->nama_pelapor, // 'nama_pelapor' dari form
                'lokasi_penemuan' => $data['lokasi_penemuan'],
                'foto' => $pathFoto, // Path foto yang sudah disimpan
                'catatan' => $request->catatan,
                'waktu_lapor' => Carbon::now(), // Gunakan Carbon::now()
                'status' => 'belum selesai',
            ]);
        }

        return redirect()->route('anggota.barang.index')
                         ->with('success', 'Barang berhasil dicatat.');
    }

    /**
     * Menandai barang TITIPAN sebagai "Selesai"
     */
    public function selesaiTitipan(Request $request, $id_barang)
    {
        // PERUBAHAN: Validasi foto base64 dihapus
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'tanggal_selesai_manual' => 'required|date',
            'waktu_selesai_jam_manual' => 'required|date_format:H:i',
        ]);

        $barang = BarangTitipan::findOrFail($id_barang);
        
        // PERUBAHAN: Logika konversi Base64 ke File DIHAPUS
        
        $waktu_selesai_gabungan = Carbon::parse($request->tanggal_selesai_manual . ' ' . $request->waktu_selesai_jam_manual);

        $barang->update([
            'status' => 'selesai',
            'waktu_selesai' => $waktu_selesai_gabungan,
            'nama_penerima' => $request->nama_penerima,
            'foto_penerima' => null, // PERUBAHAN: Dibuat null
        ]);

        return redirect()->route('anggota.barang.index')
                         ->with('success', 'Barang titipan telah ditandai selesai.');
    }

    /**
     * Menandai barang TEMUAN sebagai "Selesai" (Diperbarui untuk Base64)
     */
    public function selesaiTemuan(Request $request, $id_barang)
    {
        // PERUBAHAN: Validasi foto base64 dihapus
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'tanggal_selesai_manual' => 'required|date',
            'waktu_selesai_jam_manual' => 'required|date_format:H:i',
        ]);

        $barang = BarangTitipan::findOrFail($id_barang);
        
        // PERUBAHAN: Logika konversi Base64 ke File DIHAPUS
        
        $waktu_selesai_gabungan = Carbon::parse($request->tanggal_selesai_manual . ' ' . $request->waktu_selesai_jam_manual);

        $barang->update([
            'status' => 'selesai',
            'waktu_selesai' => $waktu_selesai_gabungan,
            'nama_penerima' => $request->nama_penerima,
            'foto_penerima' => null, // PERUBAHAN: Dibuat null
        ]);

        return redirect()->route('anggota.barang.index')
                         ->with('success', 'Barang titipan telah ditandai selesai.');
    }
}