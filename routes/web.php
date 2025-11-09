<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
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