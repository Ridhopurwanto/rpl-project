<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GangguanKamtibmasSeeder extends Seeder
{
     
    public function run(): void
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('gangguan_kamtibmas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('gangguan_kamtibmas')->insert([
            [
                'id_pengguna' => 1, 
                'waktu_lapor' => Carbon::now()->subDays(2),
                'lokasi' => 'Pagar Belakang',
                'foto' => 'gangguan/pagar_rusak.jpg',
                'deskripsi' => 'Pagar rusak diduga dijebol paksa oleh remaja sekitar',
                
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
                
                'kategori' => 'Curanmor', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}