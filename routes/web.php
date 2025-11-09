<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    if (Auth::check()) {
        // Ambil role user, default 'anggota' kalau kosong
        $role = Auth::user()->role ?? 'anggota';

        // Redirect ke dashboard sesuai role
        return redirect()->route($role . '.dashboard');
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
    
    // Halaman Dashboard Berdasarkan Role
    Route::get('/anggota/dashboard', function () {
        return view('anggota.dashboard'); // View yang akan kita buat
    })->name('anggota.dashboard');

    Route::get('/komandan/dashboard', function () {
        return view('komandan.dashboard'); // View dummy
    })->name('komandan.dashboard');

    Route::get('/bau/dashboard', function () {
        return view('bau.dashboard'); // View dummy
    })->name('bau.dashboard');

    // Route Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});