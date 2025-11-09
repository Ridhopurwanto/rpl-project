<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    if (Auth::check()) {
        // Ambil role user
        $role = Auth::user()->role;

        // --- LOGIKA BARU ---
        // Cek role dan redirect
        if ($role == 'komandan') {
            // KHUSUS KOMANDAN: Arahkan ke halaman pilih peran
            return redirect()->route('komandan.pilih-role');
        
        } elseif ($role == 'anggota') {
            // Anggota langsung ke dashboard-nya
            return redirect()->route('anggota.dashboard');
        
        } elseif ($role == 'bau') {
            // BAU langsung ke dashboard-nya
            return redirect()->route('bau.dashboard');
        
        } else {
            // Jika ada role lain atau null, default ke login
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenal.');
        }
        // --- AKHIR LOGIKA BARU ---
    }
    return redirect()->route('login');
});

// Rute untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Rute untuk yang sudah login
Route::middleware('auth')->group(function () {
    
    // --- ROUTE BARU UNTUK KOMANDAN ---
    Route::get('/komandan/pilih-role', function () {
        return view('komandan.pilih-role'); // File blade yang akan kita buat di langkah 2
    })->name('komandan.pilih-role');

    // Halaman Dashboard Berdasarkan Role
    Route::get('/anggota/dashboard', function () {
        return view('anggota.dashboard'); // File asli Anda
    })->name('anggota.dashboard');

    Route::get('/komandan/dashboard', function () {
        return view('komandan.dashboard'); // File dummy
    })->name('komandan.dashboard');

    Route::get('/bau/dashboard', function () {
        return view('bau.dashboard'); // File dummy
    })->name('bau.dashboard');

    // Rute baru untuk halaman presensi
    Route::get('/anggota/presensi', function () {
        return view('anggota.presensi'); // <-- File yang akan kita buat
    })->name('anggota.presensi');
    
    // Route Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});