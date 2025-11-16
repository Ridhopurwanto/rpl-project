<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift; // <-- KITA PAKAI LAGI MODEL INI
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManajemenShiftController extends Controller
{
    /**
     * Menampilkan halaman kalender shift untuk satu pengguna.
     * FUNGSI INI SEKARANG MENGAMBIL DATA DARI DATABASE.
     */
    public function index(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        // Ambil semua data shift milik user ini dari database
        $shifts = Shift::where('id_pengguna', $id_pengguna)->get();

        // Kirim data user dan data shift dari DB ke view
        return view('komandan.akun.shift', compact('user', 'shifts'));
    }

    /**
     * FUNGSI BARU: Untuk menyimpan perubahan shift dari Komandan.
     * Ini akan mencari shift berdasarkan pengguna & tanggal,
     * lalu MENG-UPDATE jika ada, atau MEMBUAT BARU jika belum ada.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal'     => 'required|date_format:Y-m-d',
            'jenis_shift' => 'required|in:Pagi,Malam,Off',
        ]);

        try {
            // Logic inti: Cari shift berdasarkan pengguna dan tanggal,
            // lalu update 'jenis_shift' nya.
            Shift::updateOrCreate(
                [
                    'id_pengguna' => $request->id_pengguna,
                    'tanggal'     => $request->tanggal,
                ],
                [
                    'jenis_shift' => $request->jenis_shift,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil diperbarui!',
                'tanggal' => $request->tanggal,
                'jenis_shift' => $request->jenis_shift
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui shift: ' . $e->getMessage()
            ], 500);
        }
    }
}