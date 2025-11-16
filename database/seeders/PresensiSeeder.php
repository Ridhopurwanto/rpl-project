<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('presensi')->insert([
            
            // --- Skenario 1: Anggota Jaga Satu (Shift Malam) ---
            // Asumsi id_shift = 2 adalah shift Malam untuk id_pengguna = 2
            
            // Scan 1 (Masuk)
            [
                'id_pengguna' => 2, // Anggota Jaga Satu
                'id_shift' => 2,    // Terhubung ke shift Malam
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu' => Carbon::today()->setHour(18)->setMinute(55), // Waktu scan
                'foto' => 'presensi/anggota_masuk_1.jpg',
                'status' => 'tepat waktu',
                'jenis_presensi' => 'Masuk', // Jenis scan
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Scan 2 (Masuk - Sesuai logika shift malam)
             [
                'id_pengguna' => 2, // Anggota Jaga Satu
                'id_shift' => 2,    // Terhubung ke shift Malam
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu' => Carbon::today()->setHour(22)->setMinute(0), // Waktu scan
                'foto' => 'presensi/anggota_masuk_2.jpg',
                'status' => 'tepat waktu',
                'jenis_presensi' => 'Masuk', // Jenis scan
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- Skenario 2: Komandan Utama (Shift Pagi) ---
            // Asumsi id_shift = 1 adalah shift Pagi untuk id_pengguna = 1

            // Scan 1 (Masuk)
            [
                'id_pengguna' => 1, // Komandan Utama
                'id_shift' => 1,    // Terhubung ke shift Pagi
                'nama_lengkap' => 'Komandan Utama',
                'waktu' => Carbon::today()->setHour(9)->setMinute(15), // Terlambat
                'foto' => 'presensi/komandan_masuk.jpg',
                'status' => 'terlambat',
                'jenis_presensi' => 'Masuk', // Jenis scan
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Scan 2 (Pulang)
            [
                'id_pengguna' => 1, // Komandan Utama
                'id_shift' => 1,    // Terhubung ke shift Pagi
                'nama_lengkap' => 'Komandan Utama',
                'waktu' => Carbon::today()->setHour(17)->setMinute(5), // Waktu scan pulang
                'foto' => 'presensi/komandan_pulang.jpg',
                'status' => 'tepat waktu', // Status untuk pulangnya
                'jenis_presensi' => 'Pulang', // Jenis scan
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
