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
        DB::table('gangguan_kamtibmas')->truncate();

        DB::table('gangguan_kamtibmas')->insert([
            [
                'id_pengguna' => 1, // Dilaporkan oleh Komandan
                'waktu_lapor' => Carbon::now()->subDay(),
                'lokasi' => 'Pagar Belakang',
                'foto' => 'gangguan/pagar_rusak.jpg',
                'deskripsi' => 'Pagar rusak diduga karena paksa',
                'jumlah' => 1,
                'status' => 'selesai',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2, // Dilaporkan oleh Anggota 1
                'waktu_lapor' => Carbon::now(),
                'lokasi' => 'Lampu Taman Gedung B',
                'foto' => 'gangguan/lampu_mati.jpg',
                'deskripsi' => 'Lampu taman mati total, perlu perbaikan',
                'jumlah' => 3,
                'status' => 'belum selesai',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}