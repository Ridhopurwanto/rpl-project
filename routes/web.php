<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PatroliController;
use App\Http\Controllers\KendaraanController;

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
    Route::get('/patroli', [PatroliController::class, 'index'])
         ->name('komandan.patroli');

    // Proses Update Laporan Patroli (PUT/PATCH)
    Route::put('/patroli/{id}', [PatroliController::class, 'update'])
         ->name('komandan.patroli.update');

    // Proses Hapus Laporan Patroli (DELETE)
    Route::delete('/patroli/{id}', [PatroliController::class, 'destroy'])
         ->name('komandan.patroli.destroy');

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
    Route::get('/presensi', [PresensiController::class, 'index'])
         ->name('komandan.presensi');

    // --- RUTE CRUD (HANYA UNTUK KOMANDAN) ---
    Route::delete('/presensi/{id_presensi}', [PresensiController::class, 'destroy'])
         ->name('komandan.presensi.destroy');

    // Rute untuk menyimpan perubahan (update)
    Route::put('/presensi/{id_presensi}', [PresensiController::class, 'update'])
         ->name('komandan.presensi.update');
    
        // --- LAPORAN KENDARAAN (KOMANDAN & BAU) ---
    Route::get('/kendaraan', [KendaraanController::class, 'index'])
         ->name('komandan.kendaraan');
    
    // --- CRUD KENDARAAN (HANYA KOMANDAN) ---
        Route::put('/kendaraan/log/{id_log}/update-keterangan', [KendaraanController::class, 'updateKeterangan'])
         ->name('komandan.kendaraan.log.updateKeterangan');
    Route::get('/kendaraan/master/{id_kendaraan}/edit', [KendaraanController::class, 'editMaster'])
         ->name('komandan.kendaraan.master.edit');
    Route::put('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'updateMaster'])
         ->name(name: 'komandan.kendaraan.master.update');
    Route::delete('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'destroyMaster'])
         ->name('komandan.kendaraan.master.destroy');

    // Rute baru (GET) untuk menampilkan halaman
    Route::get('/anggota/presensi', [PresensiController::class, 'index'])
         ->name('anggota.presensi');

    // Rute BARU (GET) untuk menampilkan halaman "Ambil Gambar"
    Route::get('/anggota/presensi/create', [PresensiController::class, 'create'])
         ->name('anggota.presensi.create');
         
    // Rute baru (POST) untuk tombol '+' (check-in/out)
    Route::post('/anggota/presensi', [PresensiController::class, 'store'])
         ->name('anggota.presensi.store');

    // Rute baru (GET) untuk menampilkan halaman
    Route::get('/anggota/presensi', [App\Http\Controllers\anggota\PresensiController::class, 'index'])
         ->name('anggota.presensi');

    // Rute BARU (GET) untuk menampilkan halaman "Ambil Gambar"
    Route::get('/anggota/presensi/create', [App\Http\Controllers\anggota\PresensiController::class, 'create'])
         ->name('anggota.presensi.create');
         
    // Rute baru (POST) untuk tombol '+' (check-in/out)
    Route::post('/anggota/presensi', [App\Http\Controllers\anggota\PresensiController::class, 'store'])
         ->name('anggota.presensi.store');

    // Route Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});