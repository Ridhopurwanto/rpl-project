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
        DB::table('log_kendaraan')->truncate();

        DB::table('log_kendaraan')->insert([
            [
                'id_kendaraan' => 1, // B 1234 ABC
                'id_pengguna' => 2,  // Anggota 1
                'waktu_masuk' => Carbon::today()->setHour(8)->setMinute(0),
                'waktu_keluar' => Carbon::today()->setHour(17)->setMinute(30),
                'keterangan' => 'tidak menginap',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_kendaraan' => 2, // B 5678 XYZ
                'id_pengguna' => 2,  // Anggota 1
                'waktu_masuk' => Carbon::today()->setHour(9)->setMinute(10),
                'waktu_keluar' => null,
                'keterangan' => 'menginap',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}