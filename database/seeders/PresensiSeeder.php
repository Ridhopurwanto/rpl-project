<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
     
    public function run(): void
    {
        DB::table('presensi')->insert([
            
            
            
            
            
            [
                'id_pengguna' => 2, 
                'id_shift' => 2,    
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu' => Carbon::today()->setHour(18)->setMinute(55), 
                'foto' => 'presensi/anggota_masuk_1.jpg',
                'status' => 'tepat waktu',
                'jenis_presensi' => 'Masuk', 
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            
             [
                'id_pengguna' => 2, 
                'id_shift' => 2,    
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu' => Carbon::today()->setHour(22)->setMinute(0), 
                'foto' => 'presensi/anggota_masuk_2.jpg',
                'status' => 'tepat waktu',
                'jenis_presensi' => 'Masuk', 
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            
            

            
            [
                'id_pengguna' => 1, 
                'id_shift' => 1,    
                'nama_lengkap' => 'Komandan Utama',
                'waktu' => Carbon::today()->setHour(9)->setMinute(15), 
                'foto' => 'presensi/komandan_masuk.jpg',
                'status' => 'terlambat',
                'jenis_presensi' => 'Masuk', 
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            
            [
                'id_pengguna' => 1, 
                'id_shift' => 1,    
                'nama_lengkap' => 'Komandan Utama',
                'waktu' => Carbon::today()->setHour(17)->setMinute(5), 
                'foto' => 'presensi/komandan_pulang.jpg',
                'status' => 'tepat waktu', 
                'jenis_presensi' => 'Pulang', 
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
