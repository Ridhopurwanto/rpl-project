<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogKendaraanSeeder extends Seeder
{
     
    public function run(): void
    {
        
        
        

        
        
        $kendaraan1 = DB::table('kendaraan')->where('id_kendaraan', 1)->first();
        $kendaraan2 = DB::table('kendaraan')->where('id_kendaraan', 2)->first();

        
        if (!$kendaraan1 || !$kendaraan2) {
            $this->command->error('KendaraanSeeder belum dijalankan. Melewati LogKendaraanSeeder.');
            return;
        }

        
        DB::table('log_kendaraan')->insert([
            [
                
                'id_kendaraan' => $kendaraan1->id_kendaraan,
                'nopol' => $kendaraan1->nomor_plat,
                'pemilik' => $kendaraan1->pemilik,
                'tipe' => $kendaraan1->tipe, 
                'keterangan' => 'Tidak menginap', 
                'waktu_masuk' => Carbon::today()->setHour(8)->setMinute(0),
                'waktu_keluar' => Carbon::today()->setHour(17)->setMinute(30),
                'status' => 'Keluar', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                
                
                
                
            ],
            [
                
                'id_kendaraan' => $kendaraan2->id_kendaraan,
                'nopol' => $kendaraan2->nomor_plat,
                'pemilik' => $kendaraan2->pemilik,
                'tipe' => $kendaraan2->tipe,
                'keterangan' => 'Menginap', 
                'waktu_masuk' => Carbon::today()->setHour(9)->setMinute(10),
                'waktu_keluar' => null,
                'status' => 'Masuk', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

                
                
                
            ],
        ]);
    }
}