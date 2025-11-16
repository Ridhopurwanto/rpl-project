<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan baris ini dikomentari atau dihapus,
        // karena truncate() sudah di-handle oleh DatabaseSeeder.php
        // DB::table('log_kendaraan')->truncate();

        // 1. Ambil data master dari tabel 'kendaraans'
        // Ini mengasumsikan Anda sudah menjalankan KendaraanSeeder
        $kendaraan1 = DB::table('kendaraan')->where('id_kendaraan', 1)->first();
        $kendaraan2 = DB::table('kendaraan')->where('id_kendaraan', 2)->first();

        // 2. Cek jika data kendaraan ada
        if (!$kendaraan1 || !$kendaraan2) {
            $this->command->error('KendaraanSeeder belum dijalankan. Melewati LogKendaraanSeeder.');
            return;
        }

        // 3. Masukkan data log sesuai struktur migrasi BARU
        DB::table('log_kendaraan')->insert([
            [
                // Kolom dari migrasi baru:
                'id_kendaraan' => $kendaraan1->id_kendaraan,
                'nopol' => $kendaraan1->nomor_plat,
                'pemilik' => $kendaraan1->pemilik,
                'tipe' => $kendaraan1->tipe, // Asumsi 'tipe' di 'kendaraans' adalah string
                'keterangan' => 'Tidak menginap', // 'keterangan' sekarang string
                'waktu_masuk' => Carbon::today()->setHour(8)->setMinute(0),
                'waktu_keluar' => Carbon::today()->setHour(17)->setMinute(30),
                'status' => 'Keluar', // 'status' baru (karena sudah keluar)
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                
                // KOLOM LAMA YANG DIHILANGKAN:
                // 'id_pengguna' -> Dihapus
                // 'tanggal' -> Dihapus
            ],
            [
                // Kolom dari migrasi baru:
                'id_kendaraan' => $kendaraan2->id_kendaraan,
                'nopol' => $kendaraan2->nomor_plat,
                'pemilik' => $kendaraan2->pemilik,
                'tipe' => $kendaraan2->tipe,
                'keterangan' => 'Menginap', // 'keterangan' sekarang string
                'waktu_masuk' => Carbon::today()->setHour(9)->setMinute(10),
                'waktu_keluar' => null,
                'status' => 'Masuk', // 'status' baru (default)
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

                // KOLOM LAMA YANG DIHILANGKAN:
                // 'id_pengguna' -> Dihapus
                // 'tanggal' -> Dihapus
            ],
        ]);
    }
}