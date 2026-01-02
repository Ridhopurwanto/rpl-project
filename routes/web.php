<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Komandan\PresensiController;
use App\Http\Controllers\Komandan\PatroliController;
use App\Http\Controllers\Komandan\PatroliRuleController;
use App\Http\Controllers\Komandan\KendaraanController;
use App\Http\Controllers\Komandan\TamuController;
use App\Http\Controllers\Komandan\BarangController;
use App\Http\Controllers\Komandan\GangguanKamtibmasController;
use App\Http\Controllers\Komandan\ManajemenAkunController;
use App\Http\Controllers\Komandan\ManajemenShiftController;
use App\Http\Controllers\Anggota\PresensiController as AnggotaPresensiController;
use App\Http\Controllers\Anggota\PatroliController as AnggotaPatroliController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\Anggota\KendaraanController as AnggotaKendaraanController;
use App\Http\Controllers\Anggota\TamuController as AnggotaTamuController;
use App\Http\Controllers\Anggota\GangguanKamtibmasController as AnggotaGangguanKamtibmasController;
use App\Http\Controllers\Anggota\BarangController as AnggotaBarangController;
use App\Http\Controllers\Komandan\LaporanUnduhController;
use App\Http\Controllers\ProfilController;


Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});


Route::get('/', function () {
    if (Auth::check()) {
        $peran = Auth::user()->peran;

        if ($peran == 'komandan') {
            return redirect()->route('komandan.pilih-role');
        } elseif ($peran == 'anggota') {
            return redirect()->route('anggota.dashboard');
        } elseif ($peran == 'supervisor') {
            return redirect()->route('supervisor.dashboard');
        } else {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Peran tidak dikenal.');
        }
    }
    return redirect()->route('login');
});


Route::get('/notifikasi/baca/{id}', [NotificationController::class, 'markAsRead'])
    ->middleware('auth')
    ->name('markAsRead');


Route::get('/notifikasi/baca-semua', [NotificationController::class, 'markAllRead'])
    ->middleware('auth')
    ->name('markAllRead');


Route::middleware('auth')->group(function () {

    
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
    Route::patch('/profil/account', [ProfilController::class, 'updateAccount'])->name('profil.update-account');

    
    Route::prefix('anggota')->name('anggota.')->group(function () {

        Route::get('/dashboard', function () {
            return view('anggota.dashboard');
        })->name('dashboard');

        
        Route::get('/presensi', [AnggotaPresensiController::class, 'index'])
            ->name('presensi.index');
        Route::get('/presensi/create', [AnggotaPresensiController::class, 'create'])
            ->name('presensi.create');
        Route::post('/presensi', [AnggotaPresensiController::class, 'store'])
            ->name('presensi.store');

        
        
        Route::get('/patroli', [AnggotaPatroliController::class, 'index'])
            ->name('patroli.index');
        
        
        Route::get('/patroli/create-session', [AnggotaPatroliController::class, 'createSession'])
            ->name('patroli.createSession');
        
        
        Route::get('/patroli/check-area', [AnggotaPatroliController::class, 'checkArea'])
            ->name('patroli.checkArea');
        
        
        Route::post('/patroli/claim', [AnggotaPatroliController::class, 'claimPatroli'])
            ->name('patroli.claim');

        
        Route::post('/patroli/checkpoint', [AnggotaPatroliController::class, 'storeCheckpoint'])
            ->name('patroli.storeCheckpoint');
        
        
        Route::post('/patroli/submit', [AnggotaPatroliController::class, 'submitSession'])
            ->name('patroli.submitSession');

        
        Route::get('/kendaraan', [AnggotaKendaraanController::class, 'index'])
            ->name('kendaraan.index');
        Route::get('/kendaraan/create', [AnggotaKendaraanController::class, 'create'])
            ->name('kendaraan.create');
        Route::post('/kendaraan', [AnggotaKendaraanController::class, 'store'])
            ->name('kendaraan.store');
        Route::put('/kendaraan/checkout/{id}', [AnggotaKendaraanController::class, 'checkout'])
            ->name('kendaraan.checkout');
        Route::put('/kendaraan/{id_kendaraan_log}/update-keterangan', [AnggotaKendaraanController::class, 'updateKeterangan'])
            ->name('kendaraan.updateKeterangan');

        
        Route::get('/kendaraan/search-nopol', [AnggotaKendaraanController::class, 'searchNopol'])
            ->name('kendaraan.searchNopol');

        
        Route::get('/kendaraan/riwayat', [AnggotaKendaraanController::class, 'getRiwayat'])
            ->name('kendaraan.getRiwayat');

        
        Route::get('/tamu', [AnggotaTamuController::class, 'index'])
            ->name('tamu.index');
        Route::get('/tamu/create', [AnggotaTamuController::class, 'create'])
            ->name('tamu.create');
        Route::post('/tamu', [AnggotaTamuController::class, 'store'])
            ->name('tamu.store');

        
        Route::get('/gangguan-kamtibmas', [AnggotaGangguanKamtibmasController::class, 'index'])
            ->name('gangguan.index');
        Route::get('/gangguan-kamtibmas/create', [AnggotaGangguanKamtibmasController::class, 'create'])
            ->name('gangguan.create');
        Route::post('/gangguan-kamtibmas', [AnggotaGangguanKamtibmasController::class, 'store'])
            ->name('gangguan.store');

        
        Route::get('/barang', [AnggotaBarangController::class, 'index'])
            ->name('barang.index');
        Route::get('/barang/create', [AnggotaBarangController::class, 'create'])
            ->name('barang.create');
        Route::post('/barang', [AnggotaBarangController::class, 'store'])
            ->name('barang.store');
        Route::put('/barang-titipan/{id_barang}/selesai', [AnggotaBarangController::class, 'selesaiTitipan'])
            ->name('barang.selesaiTitipan')
            ->whereNumber('id_barang');
        Route::put('/barang-temuan/{id_barang}/selesai', [AnggotaBarangController::class, 'selesaiTemuan'])
            ->name('barang.selesaiTemuan')
            ->whereNumber('id_barang');
        Route::get('/barang/search', [AnggotaBarangController::class, 'searchBarang'])
            ->name('barang.search');
        Route::get('/barang/riwayat', [AnggotaBarangController::class, 'getRiwayat'])
            ->name('barang.getRiwayat');
    });

    
    Route::prefix('komandan')->name('komandan.')->group(function () {

        Route::get('/pilih-role', function () {
            return view('komandan.pilih-role');
        })->name('pilih-role');

        Route::get('/dashboard', function () {
            return view('komandan.dashboard');
        })->name('dashboard');

        
        Route::get('/presensi', [PresensiController::class, 'index'])
            ->name('presensi');
        Route::put('/presensi/update-rules', [PresensiController::class, 'updateRules'])
            ->name('presensi.updateRules');
        Route::delete('/presensi/{id_presensi}', [PresensiController::class, 'destroy'])
            ->name('presensi.destroy');
        Route::put('/presensi/{id_presensi}', [PresensiController::class, 'update'])
            ->name('presensi.update');

        
        Route::get('/patroli', [PatroliController::class, 'index'])
            ->name('patroli');
        Route::put('/patroli/{id}', [PatroliController::class, 'update'])
            ->name('patroli.update');
        Route::delete('/patroli/{id}', [PatroliController::class, 'destroy'])
            ->name('patroli.destroy');
        
        
        Route::post('/patroli/update-rules', [PatroliRuleController::class, 'updateRules'])
            ->name('patroli.updateRules');

        
        Route::get('/kendaraan', [KendaraanController::class, 'index'])
            ->name('kendaraan');
        Route::put('/kendaraan/log/{id_log}/update-keterangan', [KendaraanController::class, 'updateKeterangan'])
            ->name('kendaraan.log.updateKeterangan');
        Route::delete('/kendaraan/log/{id_log}', [KendaraanController::class, 'destroyLog'])
            ->name('kendaraan.log.delete');
        Route::get('/kendaraan/master/{id_kendaraan}/edit', [KendaraanController::class, 'editMaster'])
            ->name('kendaraan.master.edit');
        Route::put('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'updateMaster'])
            ->name('kendaraan.master.update');
        Route::delete('/kendaraan/master/{id_kendaraan}', [KendaraanController::class, 'destroyMaster'])
            ->name('kendaraan.master.destroy');
        Route::post('/kendaraan/log/{id_log}/promote', [KendaraanController::class, 'promoteLogToMaster'])
            ->name('kendaraan.log.promote');
        Route::get('/kendaraan/search-riwayat', [KendaraanController::class, 'searchRiwayat'])
            ->name('kendaraan.searchRiwayat');
        Route::get('/kendaraan/search-master', [KendaraanController::class, 'searchMaster'])
            ->name('kendaraan.searchMaster');

        
        Route::get('/tamu', [TamuController::class, 'index'])
            ->name('tamu');
        Route::put('/tamu/{id_tamu}', [TamuController::class, 'update'])
            ->name('tamu.update');
        Route::delete('/tamu/{id_tamu}', [TamuController::class, 'destroy'])
            ->name('tamu.destroy');

        
        Route::get('/barang', [BarangController::class, 'index'])
            ->name('barang');
        Route::put('/barang/titipan/{id}/edit-penerima', [BarangController::class, 'updatePenerima'])
            ->name('barang.updatePenerima');
        Route::put('/barang/{type}/{id}/edit', [BarangController::class, 'updateBarang'])
            ->name('barang.update')
            ->where('type', 'temuan|titipan');

        
        Route::get('/gangguan', [GangguanKamtibmasController::class, 'index'])
            ->name('gangguan');
        Route::put('/gangguan/{id_gangguan}', [GangguanKamtibmasController::class, 'update'])
            ->name('gangguan.update');
        Route::delete('/gangguan/{id_gangguan}', [GangguanKamtibmasController::class, 'destroy'])
            ->name('gangguan.destroy');

        
        Route::resource('akun', ManajemenAkunController::class)->except(['show']);

        Route::get('akun/{id_pengguna}/shift', [ManajemenShiftController::class, 'index'])
            ->name('akun.shift');

        Route::post('/set-role', [RoleSwitchController::class, 'setRole'])
            ->name('role.set');

        
        Route::post('akun/shift/update', [ManajemenShiftController::class, 'update'])
            ->name('akun.shift.update');
        
        
        Route::post('akun/shift/reset', [ManajemenShiftController::class, 'reset'])
            ->name('akun.shift.reset');

        
        Route::get('/unduh', [LaporanUnduhController::class, 'index'])
            ->name('unduh');

        Route::post('/laporan/download', [LaporanUnduhController::class, 'download'])
            ->name('laporan.download');

        Route::get('/laporan/download-single', [LaporanUnduhController::class, 'downloadSatuan'])
            ->name('laporan.download-single');
    });

    
    
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', function () {
            
            return view('supervisor.dashboard');
        })->name('dashboard');

        
        
        
        
        Route::get('/akun', [App\Http\Controllers\Supervisor\ManajemenAkunController::class, 'index'])->name('akun.index');
        Route::get('akun/{id_pengguna}/shift', [App\Http\Controllers\Supervisor\ManajemenShiftController::class, 'index'])->name('akun.shift');
        Route::post('akun/shift/update', [App\Http\Controllers\Supervisor\ManajemenShiftController::class, 'update'])->name('akun.shift.update');
        Route::post('akun/shift/reset', [App\Http\Controllers\Supervisor\ManajemenShiftController::class, 'reset'])->name('akun.shift.reset');

        
        Route::get('/presensi', [App\Http\Controllers\Supervisor\PresensiController::class, 'index'])->name('presensi.index');

        
        Route::get('/patroli', [App\Http\Controllers\Supervisor\PatroliController::class, 'index'])->name('patroli.index');

        
        Route::get('/kendaraan', [App\Http\Controllers\Supervisor\KendaraanController::class, 'index'])->name('kendaraan.index');
        Route::get('/kendaraan/search-riwayat', [App\Http\Controllers\Supervisor\KendaraanController::class, 'searchRiwayat'])->name('kendaraan.searchRiwayat');
        Route::get('/kendaraan/search-master', [App\Http\Controllers\Supervisor\KendaraanController::class, 'searchMaster'])->name('kendaraan.searchMaster');


        
        Route::get('/tamu', [App\Http\Controllers\Supervisor\TamuController::class, 'index'])->name('tamu.index');

        
        Route::resource('barang', App\Http\Controllers\Supervisor\BarangController::class)->only(['index']);

        
        Route::resource('gangguan', App\Http\Controllers\Supervisor\GangguanKamtibmasController::class)->parameters(['gangguan' => 'gangguan']); 

        
        Route::get('/unduh', [App\Http\Controllers\Supervisor\LaporanUnduhController::class, 'index'])->name('unduh');
        Route::post('/laporan/download', [App\Http\Controllers\Supervisor\LaporanUnduhController::class, 'download'])->name('laporan.download');
        Route::get('/laporan/download-single', [App\Http\Controllers\Supervisor\LaporanUnduhController::class, 'downloadSatuan'])->name('laporan.download-single');
    });

    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
