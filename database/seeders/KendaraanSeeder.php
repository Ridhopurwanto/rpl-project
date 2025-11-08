<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('kendaraan')->insert([
            [
                'id_kendaraan' => 1,
                'nomor_plat' => 'B 1234 ABC',
                'pemilik' => 'John Doe',
                'tipe' => 'Roda 4', // Sesuai enum di SQL
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_kendaraan' => 2,
                'nomor_plat' => 'B 5678 XYZ',
                'pemilik' => 'Jane Smith',
                'tipe' => 'Roda 2', // Sesuai enum di SQL
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_kendaraan' => 3,
                'nomor_plat' => 'F 4444 GA',
                'pemilik' => 'Tamu Staff',
                'tipe' => 'Roda 4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}