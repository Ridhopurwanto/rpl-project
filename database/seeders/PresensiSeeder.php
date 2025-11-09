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
        DB::table('presensi')->truncate();

        DB::table('presensi')->insert([
            [
                'id_pengguna' => 2, // Anggota 1
                'nama_lengkap' => 'Anggota Jaga Satu', // Denormalisasi
                'waktu_masuk' => Carbon::today()->setHour(7)->setMinute(55),
                'foto_masuk' => 'presensi/masuk_1.jpg',
                'waktu_pulang' => Carbon::today()->setHour(18)->setMinute(5),
                'foto_pulang' => 'presensi/pulang_1.jpg',
                'lokasi' => 'Gerbang Depan',
                'status' => 'tepat waktu',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 1, // Komandan
                'nama_lengkap' => 'Komandan Utama', // Denormalisasi
                'waktu_masuk' => Carbon::today()->setHour(9)->setMinute(15),
                'foto_masuk' => 'presensi/masuk_2.jpg',
                'waktu_pulang' => null,
                'foto_pulang' => null,
                'lokasi' => 'Pos Komando',
                'status' => 'terlambat',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}