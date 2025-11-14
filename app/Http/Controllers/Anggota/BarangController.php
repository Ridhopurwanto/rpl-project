<?php
// app/Http/Controllers/Anggota/BarangController.php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman index (3 tabel)
     */
    public function index(Request $request)
    {
        $barang_titipan = Barang::where('kategori', 'titipan')
                                ->where('status', 'belum selesai')
                                ->orderBy('waktu_lapor', 'desc')
                                ->get();
        
        $barang_temuan = Barang::where('kategori', 'temuan')
                               ->where('status', 'belum selesai')
                               ->orderBy('waktu_lapor', 'desc')
                               ->get();

        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_riwayat = $request->input('kategori_riwayat', 'semua');

        $query_riwayat = Barang::where('status', 'selesai')
                               ->whereDate('waktu_selesai', $tanggal_riwayat);

        if ($kategori_riwayat && $kategori_riwayat != 'semua') {
            $query_riwayat->where('kategori', $kategori_riwayat);
        }

        $riwayat_barang = $query_riwayat->orderBy('waktu_selesai', 'desc')->get();

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
     * Menyimpan barang baru
     */
    public function store(Request $request)
    {
        // Validasi yang sudah diperbarui
        $request->validate([
            'kategori' => 'required|in:titip,temu',
            'nama_barang' => 'required|string|max:255', // Diperbarui
            'catatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nama_pelapor' => 'required|string|max:255',
            'tujuan' => 'required_if:kategori,titip|nullable|string|max:255',
            'lokasi_penemuan' => 'required_if:kategori,temu|nullable|string|max:255', // Diperbarui
        ]);

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto_barang', 'public');
        }

        // Penyimpanan yang sudah diperbarui
        Barang::create([
            'kategori' => $request->kategori,
            'id_pengguna' => Auth::id(),
            'nama_barang' => $request->nama_barang, // Diperbarui
            'lokasi_penemuan' => $request->lokasi_penemuan, // Diperbarui
            'tujuan' => $request->tujuan,
            'nama_pelapor' => $request->nama_pelapor,
            'waktu_lapor' => Carbon::now(),
            'status' => 'belum selesai',
            'foto' => $pathFoto,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('anggota.barang.index')
                         ->with('success', 'Barang berhasil dicatat.');
    }

    /**
     * Menandai barang sebagai "Selesai" (diambil)
     */
    public function selesai(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
        ]);

        $barang = Barang::where('id_barang', $id_barang)
                        ->where('status', 'belum selesai')
                        ->firstOrFail();

        $barang->update([
            'status' => 'selesai',
            'waktu_selesai' => Carbon::now(),
            'nama_penerima' => $request->nama_penerima,
        ]);

        return redirect()->route('anggota.barang.index')
                         ->with('success', 'Barang telah ditandai selesai.');
    }
}