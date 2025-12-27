<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftRuleSeeder extends Seeder
{
     
    public function run(): void
    {
        
        

        DB::table('shift_rule')->insert([
            [
                'idshift_rule' => 1,
                'jenis_shift' => 'Pagi',
                'jam_masuk' => '07:00:00',
                'jam_keluar' => '19:00:00',
                'toleransi' => 10,
                'dibuka' => 120,
                'is_geotag_enabled' => 1,
            ],
            [
                'idshift_rule' => 2,
                'jenis_shift' => 'Malam',
                'jam_masuk' => '19:00:00',
                'jam_keluar' => '07:00:00', 
                'toleransi' => 10,
                'dibuka' => 120,
                'is_geotag_enabled' => 1,
            ],
            [
                'idshift_rule' => 3,
                'jenis_shift' => 'Off',
                'jam_masuk' => '00:00:00',
                'jam_keluar' => '00:00:00',
                'toleransi' => null,
                'dibuka' => null,              
                'is_geotag_enabled' => null,
            ],
            [
                'idshift_rule' => 4,
                'jenis_shift' => 'Non Shift',
                'jam_masuk' => '07:00:00',
                'jam_keluar' => '17:00:00',
                'toleransi' => 10,
                'dibuka' => 120,
                'is_geotag_enabled' => 1,
            ],
        ]);
    }
}