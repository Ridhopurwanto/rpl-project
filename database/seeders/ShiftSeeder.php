<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
     
    public function run(): void
    {
       

        DB::table('shift')->insert([
            [
                'id_pengguna' => 1, 
                'tanggal' => Carbon::today(),
                'jenis_shift' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2, 
                'tanggal' => Carbon::today(),
                'jenis_shift' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 3, 
                'tanggal' => Carbon::today(),
                'jenis_shift' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}