<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // DB::table('shift')->truncate();

        DB::table('shift')->insert([
            [
                'id_pengguna' => 1, // Komandan
                'tanggal' => Carbon::today(),
                'jenis_shift' => 'Pagi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2, // Anggota 1
                'tanggal' => Carbon::today(),
                'jenis_shift' => 'Malam',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 3, // BAU
                'tanggal' => Carbon::today(),
                'jenis_shift' => 'Off',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}