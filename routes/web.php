<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PatroliController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\Anggota\PresensiController as AnggotaPresensiController;
use App\Http\Controllers\Anggota\PatroliController as AnggotaPatroliController;
use App\Http\Controllers\RoleSwitchController;

// Rute untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Rute utama (akan me-redirect jika sudah login)
Route::get('/', function () {
    if (Auth::check()) {
        $peran = Auth::user()->peran;

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


// Rute untuk yang sudah login
Route::middleware('auth')->group(function () {

    // --- RUTE UNTUK ANGGOTA ---
    Route::prefix('anggota')->name('anggota.')->group(function () {
        
        Route::get('/dashboard', function () {
            return view('anggota.dashboard');
        })->name('dashboard');

        // Presensi Anggota
        Route::get('/presensi', [AnggotaPresensiController::class, 'index'])
            ->name('presensi.index'); // DIUBAH: Dulu 'anggota.presensi'
        Route::get('/presensi/create', [AnggotaPresensiController::class, 'create'])
            ->name('presensi.create');
        Route::post('/presensi', [AnggotaPresensiController::class, 'store'])
            ->name('presensi.store');

        // Patroli Anggota
        Route::get('/patroli', [AnggotaPatroliController::class, 'index'])
            ->name('patroli.index');
        Route::get('/patroli/create-session', [AnggotaPatroliController::class, 'createSession'])
            ->name('patroli.createSession');
        Route::get('/patroli/create-checkpoint', [AnggotaPatroliController::class, 'createCheckpoint'])
            ->name('patroli.createCheckpoint');
        Route::post('/patroli/store-checkpoint', [AnggotaPatroliController::class, 'storeCheckpoint'])
            ->name('patroli.storeCheckpoint');
        Route::post('/patroli/submit-session', [AnggotaPatroliController::class, 'submitSession'])
            ->name('patroli.submitSession');
    });

    // --- RUTE UNTUK KOMANDAN (CRUD & Manajemen) ---
    Route::prefix('komandan')->name('komandan.')->group(function () {
        
        Route::get('/pilih-role', function () {
            return view('komandan.pilih-role');
        })->name('pilih-role');

        Route::get('/dashboard', function () {
            return view('komandan.dashboard');
        })->name('dashboard');

        Route::get('/presensi', [PresensiController::class, 'index'])
            ->name('presensi'); // NAMA DIUBAH agar jelas

        Route::get('/patroli', [PatroliController::class, 'index'])
            ->name('patroli'); // NAMA DIUBAH agar jelas

        Route::get('/kendaraan', [KendaraanController::class, 'index'])
            ->name('kendaraan'); // NAMA DIUBAH agar jelas

        // Rute untuk menangani perpindahan role oleh Komandan
        Route::post('/set-role', [RoleSwitchController::class, 'setRole'])
            ->name('role.set');

        // CRUD Patroli
        Route::put('/patroli/{id}', [PatroliController::class, 'update'])
            ->name('patroli.update');
        Route::delete('/patroli/{id}', [PatroliController::class, 'destroy'])
            ->name('patroli.destroy');
        
        // CRUD Presensi
        Route::delete('/presensi/{id_presensi}', [PresensiController::class, 'destroy'])
            ->name('presensi.destroy');
        Route::put('/presensi/{id_presensi}', [PresensiController::class, 'update'])
            ->name('presensi.update');
        
        // CRUD Kendaraan
        Route::put('/kendaraan/log/{id_log}/update-keterangan', [KendaraanController::class, 'updateKeterangan'])
            ->name('kendaraan.log.updateKeterangan');
        Route::get('/kendaraan/master/{id_kendaraan}/edit', [KendaraanController::class, 'editMaster'])
            ->name('kendaraan.master.edit');
        Route::put('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'updateMaster'])
            ->name('kendaraan.master.update');
        Route::delete('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'destroyMaster'])
            ->name('kendaraan.master.destroy');
    });

    // --- RUTE UNTUK BAU ---
    Route::prefix('bau')->name('bau.')->group(function () {
        Route::get('/dashboard', function () {
            return view('bau.dashboard');
        })->name('dashboard');
    });

    // --- RUTE LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});