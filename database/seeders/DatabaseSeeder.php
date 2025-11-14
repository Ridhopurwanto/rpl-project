<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Pastikan ini ada
use Illuminate\Support\Facades\DB;     // Pastikan ini ada

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan semua tabel (truncate)
        //    DIMULAI DARI TABEL ANAK (CHILD) TERLEBIH DAHULU
        //    (Tabel yang punya foreign key)
        DB::table('shift')->truncate();
        DB::table('presensi')->truncate();
        DB::table('patroli')->truncate();
        DB::table('log_kendaraan')->truncate(); // Anak dari pengguna & kendaraan
        DB::table('tamu')->truncate();
        DB::table('barang_temu')->truncate();
        DB::table('barang_titip')->truncate();
        DB::table('gangguan_kamtibmas')->truncate();
        DB::table('barang')->truncate();

        //    BARU KOSONGKAN TABEL INDUK (PARENT)
        //    (Tabel yang jadi referensi foreign key)
        DB::table('pengguna')->truncate();
        DB::table('kendaraan')->truncate();

        // 3. Nyalakan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        // 4. Panggil semua seeder
        $this->call([
            PenggunaSeeder::class,
            KendaraanSeeder::class,
            ShiftSeeder::class,
            PresensiSeeder::class,
            PatroliSeeder::class,
            LogKendaraanSeeder::class,
            TamuSeeder::class,
            BarangTemuSeeder::class,
            BarangTitipSeeder::class,
            GangguanKamtibmasSeeder::class,
            BarangSeeder::class,
        ]);
    }
}