<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GangguanKamtibmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama (opsional, hati-hati jika production)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('gangguan_kamtibmas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('gangguan_kamtibmas')->insert([
            [
                'id_pengguna' => 1, // Komandan
                'waktu_lapor' => Carbon::now()->subDays(2),
                'lokasi' => 'Pagar Belakang',
                'foto' => 'gangguan/pagar_rusak.jpg',
                'deskripsi' => 'Pagar rusak diduga dijebol paksa oleh remaja sekitar',
                // Kita masukkan ke 'Kenakalan Remaja' karena 'Pagar Rusak' paling mendekati ini
                'kategori' => 'Kenakalan Remaja', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 1, 
                'waktu_lapor' => Carbon::now()->subHours(5),
                'lokasi' => 'Parkiran Utama',
                'foto' => 'gangguan/motor_hilang.jpg',
                'deskripsi' => 'Laporan kehilangan motor Honda Beat nopol B 1234 CD',
                // Contoh kategori spesifik
                'kategori' => 'Curanmor', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}