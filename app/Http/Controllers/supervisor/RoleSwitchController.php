<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleSwitchController extends Controller
{
    /**
     * Menyimpan peran yang sedang "di-impersonasi" ke dalam session.
     */
    public function setRole(Request $request)
    {
        // Validasi
        $request->validate([
            'role' => 'required|string|in:komandan,anggota,bau',
        ]);

        $user = Auth::user();
        $desiredRole = $request->input('role');

        // Keamanan: Hanya Komandan yang boleh ganti-ganti ke 'anggota'
        if ($user->peran != 'komandan' && $desiredRole == 'anggota') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // --- INI BAGIAN PENTING ---
        // Simpan peran yang sedang aktif di session
        Session::put('current_role', $desiredRole);

        // Redirect ke dashboard yang sesuai
        if ($desiredRole == 'komandan') {
            return redirect()->route('komandan.dashboard');
        } elseif ($desiredRole == 'anggota') {
            return redirect()->route('anggota.dashboard');
        }

        return redirect('/'); // Fallback
    }
}