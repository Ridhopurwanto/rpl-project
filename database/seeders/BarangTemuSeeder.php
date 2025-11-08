<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangTemuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barang_temu')->truncate();

        DB::table('barang_temu')->insert([
            [
                'nama_pelapor' => 'Satpam Internal',
                'id_pengguna' => 2, // Dicatat oleh Anggota 1
                'waktu_lapor' => Carbon::now()->subHour(),
                'waktu_selesai' => null,
                'status' => 'belum selesai',
                'foto' => 'temuan/dompet_1.jpg',
                'catatan' => 'Ditemukan dompet hitam di lobi gedung A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}