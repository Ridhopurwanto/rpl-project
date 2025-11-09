<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
<<<<<<< HEAD
use App\Http\Controllers\PresensiController; 
use App\Http\Controllers\LaporanPatroliController;
=======
use App\Http\Controllers\PresensiController;
>>>>>>> d0f1f36263652bff7db4d805566e9b83ab6a5604

Route::get('/', function () {
    if (Auth::check()) {
        // Ambil role user
        // PERBAIKAN: Gunakan 'peran' sesuai AuthenticatedSessionController
        $peran = Auth::user()->peran; 

        // --- LOGIKA BARU ---
        // Cek peran dan redirect
        if ($peran == 'komandan') {
            return redirect()->route('komandan.pilih-role');
        
        } elseif ($peran == 'anggota') {
            return redirect()->route('anggota.dashboard');
        
        } elseif ($peran == 'bau') {
            return redirect()->route('bau.dashboard');
        
        } else {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Peran tidak dikenal.');
        }
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
    
    // --- ROUTE UNTUK KOMANDAN ---
    Route::get('/komandan/pilih-role', function () {
        return view('komandan.pilih-role'); // File blade dari
    })->name('komandan.pilih-role');

    Route::get('/komandan/dashboard', function () {
        return view('komandan.dashboard'); // File yang akan kita buat
    })->name('komandan.dashboard');

    // Halaman Laporan Patroli (GET)
    Route::get('/laporan/patroli', [LaporanPatroliController::class, 'index'])
         ->name('laporan.patroli');

    // Proses Update Laporan Patroli (PUT/PATCH)
    Route::put('/laporan/patroli/{id}', [LaporanPatroliController::class, 'update'])
         ->name('laporan.patroli.update');

    // Proses Hapus Laporan Patroli (DELETE)
    Route::delete('/laporan/patroli/{id}', [LaporanPatroliController::class, 'destroy'])
         ->name('laporan.patroli.destroy');

    // --- ROUTE UNTUK ANGGOTA ---
    Route::get('/anggota/dashboard', function () {
        return view('anggota.dashboard'); // File asli Anda
    })->name('anggota.dashboard');

    Route::get('/anggota/presensi', [PresensiController::class, 'createForAnggota'])
         ->name('anggota.presensi');
    Route::post('/anggota/presensi', [PresensiController::class, 'storeForAnggota'])
         ->name('anggota.presensi.store');

         
    // --- ROUTE UNTUK BAU ---
    Route::get('/bau/dashboard', function () {
        return view('bau.dashboard'); // File yang akan kita buat
    })->name('bau.dashboard');


    // --- RUTE LAPORAN (UNTUK KOMANDAN & BAU) ---
    Route::get('/laporan/presensi', [PresensiController::class, 'index'])
         ->name('laporan.presensi');

    // --- RUTE CRUD (HANYA UNTUK KOMANDAN) ---
    Route::delete('/laporan/presensi/{id_presensi}', [PresensiController::class, 'destroy'])
         ->name('laporan.presensi.destroy');

    Route::get('/laporan/presensi/{id_presensi}/edit', [PresensiController::class, 'edit'])
         ->name('laporan.presensi.edit');

    // Rute untuk menyimpan perubahan (update)
    Route::put('/laporan/presensi/{id_presensi}', [PresensiController::class, 'update'])
         ->name('laporan.presensi.update');
    

    // Route Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});