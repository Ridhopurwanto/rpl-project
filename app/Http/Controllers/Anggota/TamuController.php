<?php
// app/Http/Controllers/Anggota/TamuController.php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TamuController extends Controller
{
    /**
     * Menampilkan halaman riwayat tamu (sesuai desain)
     */
    public function index(Request $request)
    {
        // 1. Tentukan tanggal filter
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());

        // 2. Ambil riwayat tamu pada tanggal tersebut
        // (Berdasarkan 'waktu_datang' karena 'waktu_pulang' tidak ada)
        $riwayat_tamu = Tamu::whereDate('waktu_datang', $tanggal_riwayat)
                            ->orderBy('waktu_datang', 'desc')
                            ->get();

        // 3. Kirim data ke view
        return view('anggota.tamu-index', [
            'riwayat_tamu' => $riwayat_tamu,
            'tanggal_terpilih' => $tanggal_riwayat,
        ]);
    }

    /**
     * Menampilkan form tambah tamu
     */
    public function create()
    {
        return view('anggota.tamu-create');
    }

    /**
     * Menyimpan data tamu baru
     */
    public function store(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'tanggal_kunjungan' => 'required|date', // <-- Validasi baru
            'jam_kunjungan' => 'required|date_format:H:i', // <-- Validasi baru
            'tujuan' => 'required|string|max:255',
        ]);

        // 2. Gabungkan tanggal dan jam menjadi satu datetime object
        $waktu_datang_gabungan = Carbon::parse($request->tanggal_kunjungan . ' ' . $request->jam_kunjungan);

        // 3. Simpan ke database
        Tamu::create([
            'nama_tamu' => $request->nama_tamu,
            'instansi' => $request->instansi,
            'tujuan' => $request->tujuan,
            'waktu_datang' => $waktu_datang_gabungan, // Gunakan yang sudah digabung
            'id_pengguna' => Auth::id(), // ID Anggota yang login
        ]);

        // 4. Redirect ke halaman index
        return redirect()->route('anggota.tamu.index')
                         ->with('success', 'Data tamu berhasil ditambahkan.');
    }
}