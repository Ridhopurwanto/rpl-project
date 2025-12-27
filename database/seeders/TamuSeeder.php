<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TamuSeeder extends Seeder
{
     
    public function run(): void
    {
        DB::table('tamu')->truncate();

        DB::table('tamu')->insert([
            [
                'nama_tamu' => 'Budi Santoso',
                'instansi' => 'PT. Mitra Jaya',
                'tujuan' => 'Meeting dengan BAU',
                'id_pengguna' => 3, 
                'waktu_datang' => Carbon::today()->setHour(10)->setMinute(0),
                'waktu_pulang' => Carbon::today()->setHour(11)->setMinute(30),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_tamu' => 'Siti Aminah',
                'instansi' => 'Vendor Catering',
                'tujuan' => 'Antar makan siang',
                'id_pengguna' => 2, 
                'waktu_datang' => Carbon::today()->setHour(11)->setMinute(45),
                'waktu_pulang' => null, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}