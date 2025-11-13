<?php
// app/Http/Controllers/Anggota/GangguanKamtibmasController.php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\GangguanKamtibmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Penting untuk file
use Carbon\Carbon;
use Illuminate\Support\Str; 

class GangguanKamtibmasController extends Controller
{
    /**
     * Menampilkan halaman riwayat gangguan (sesuai desain)
     */
    public function index(Request $request)
    {
        // 1. Tentukan tanggal filter (range)
        $tanggal_mulai = $request->input('tanggal_mulai', Carbon::today()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', Carbon::today()->toDateString());
        $kategori = $request->input('kategori');

        // 2. Query data
        $query = GangguanKamtibmas::query();

        // Filter berdasarkan range tanggal 'waktu_lapor'
        $query->whereBetween('waktu_lapor', [
            Carbon::parse($tanggal_mulai)->startOfDay(),
            Carbon::parse($tanggal_selesai)->endOfDay()
        ]);

        // Filter berdasarkan kategori (deskripsi) jika ada
        if ($kategori && $kategori != 'semua') {
            $query->where('kategori', $kategori);
        }

        $laporan_gangguan = $query->orderBy('waktu_lapor', 'desc')->get();

        // 3. Kirim data ke view
        return view('anggota.gangguan-index', [
            'laporan_gangguan' => $laporan_gangguan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'kategori_terpilih' => $kategori,
        ]);
    }

    /**
     * Menampilkan form tambah laporan
     */
    public function create()
    {
        return view('anggota.gangguan-create');
    }

    /**
     * Menyimpan laporan baru
     */
    public function store(Request $request)
    {
        // 1. Validasi (Disesuaikan dengan field baru)
        $request->validate([
            // 'foto' (file) diganti dengan 'foto_base64' (string)
            'foto_base64' => 'required|string', 
            
            'tanggal_lapor' => 'required|date',
            'waktu_lapor_time' => 'required|date_format:H:i',
            'kategori' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        // 2. Handle File Upload (Logika Baru untuk Base64)
        $pathFoto = null;
        if ($request->foto_base64) {
            // Pisahkan data 'data:image/jpeg;base64,' dari string
            list($type, $data) = explode(';', $request->foto_base64);
            list(, $data) = explode(',', $data);
            $imageData = base64_decode($data);
            
            // Buat nama file unik
            $filename = 'foto_gangguan/' . Str::random(20) . '.jpg';
            
            // Simpan file ke storage
            Storage::disk('public')->put($filename, $imageData);
            $pathFoto = $filename;
        }

        // 3. Gabungkan Tanggal dan Waktu
        $waktu_lapor_gabungan = Carbon::parse($request->tanggal_lapor . ' ' . $request->waktu_lapor_time);

        // 4. Simpan ke database
        GangguanKamtibmas::create([
            'id_pengguna' => Auth::id(),
            'waktu_lapor' => $waktu_lapor_gabungan,
            'lokasi' => $request->lokasi,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'foto' => $pathFoto,
        ]);

        // 5. Redirect
        return redirect()->route('anggota.gangguan.index')
                         ->with('success', 'Laporan gangguan berhasil ditambahkan.');
    }
}