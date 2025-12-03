<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Kosongkan tabel terlebih dahulu agar tidak duplikat saat di-seed ulang
        // DB::table('shift_rule')->truncate(); 

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
                'jam_keluar' => '07:00:00', // Besok pagi
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