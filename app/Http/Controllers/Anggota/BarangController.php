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
            'lokasi_tujuan' => 'required',
            'foto_base64' => 'required',// <-- PERUBAHAN 1: Validasi sebagai string (Base64)
            // 'tanggal' => 'nullable|date', // Tambahkan ini jika Anda tetap menggunakan field tanggal
        ]);

        try {
            // 1. Decode Foto Base64
            $image_parts = explode(";base64,", $request->foto_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            // 2. Simpan Foto
            $fileName = 'barang_' . uniqid() . '.' . $image_type;
            $path = 'foto_barang/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 3. Logika Simpan Berdasarkan Kategori
            if ($request->kategori == 'temuan') {
                BarangTemuan::create([
                    'nama_barang' => $request->nama_barang,
                    'nama_pelapor' => $request->nama_pelapor,
                    'lokasi_penemuan' => $request->lokasi_tujuan, // Mapping field
                    'waktu_lapor' => now(), // Atau ambil dari request jika ada input tanggal manual
                    'foto' => $path,
                    'catatan' => $request->catatan,
                    'status' => 'belum selesai', // Sesuaikan dengan struktur DB Anda
                    'id_pengguna' => Auth::id(), // Jika ada kolom relasi user
                ]);
            } else {
                // Barang Titipan
                BarangTitipan::create([
                    'nama_barang' => $request->nama_barang,
                    'nama_penitip' => $request->nama_pelapor, // Mapping field
                    'tujuan' => $request->lokasi_tujuan,     // Mapping field
                    'waktu_titip' => now(),
                    'foto' => $path,
                    'catatan' => $request->catatan,
                    'status' => 'belum selesai',
                    'id_pengguna' => Auth::id(), // Jika ada kolom relasi user
                ]);
            }

            return redirect()->route('anggota.barang.index')->with('success', 'Data barang berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang TITIPAN sebagai "Selesai"
     */
    public function selesaiTitipan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required',
            'foto_penerima_base64' => 'required', // Foto bukti serah terima
            'tanggal_ambil' => 'required|date',
            'waktu_ambil' => 'required',
        ]);

        try {
            // 1. Proses Foto Penerima
            $image_parts = explode(";base64,", $request->foto_penerima_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'penerima_titipan_' . uniqid() . '.' . $image_type;
            $path = 'foto_penerima/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 2. Gabung Waktu
            $waktu_selesai = Carbon::parse($request->tanggal_ambil . ' ' . $request->waktu_ambil);

            // 3. Update Database
            $barang = BarangTitipan::findOrFail($id_barang);
            $barang->update([
                'nama_penerima' => $request->nama_penerima,
                'foto_penerima' => $path,
                'waktu_selesai' => $waktu_selesai, // Pastikan kolom ini ada di DB
                'status' => 'selesai',
            ]);

            return redirect()->back()->with('success', 'Barang titipan telah diselesaikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang TEMUAN sebagai "Selesai" (Diperbarui untuk Base64)
     */
    public function selesaiTemuan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required',
            'foto_penerima_base64' => 'required',
            'tanggal_ambil' => 'required|date',
            'waktu_ambil' => 'required',
        ]);

        try {
            // 1. Proses Foto Penerima
            $image_parts = explode(";base64,", $request->foto_penerima_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'penerima_temuan_' . uniqid() . '.' . $image_type;
            $path = 'foto_penerima/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 2. Gabung Waktu
            $waktu_selesai = Carbon::parse($request->tanggal_ambil . ' ' . $request->waktu_ambil);

            // 3. Update Database
            $barang = BarangTemuan::findOrFail($id_barang);
            $barang->update([
                'nama_penerima' => $request->nama_penerima,
                'foto_penerima' => $path,
                'waktu_selesai' => $waktu_selesai,
                'status' => 'selesai',
            ]);

            return redirect()->back()->with('success', 'Barang temuan telah diselesaikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}