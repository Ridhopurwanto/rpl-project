<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangTitipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barang_titip')->truncate();

        DB::table('barang_titip')->insert([
            [
                'id_pengguna' => 2,
                'nama_barang' => 'Paket Shopee',
                'nama_penitip' => 'J&T Express', // <-- Kolom baru
                'tujuan' => 'Kessya (Kamar 201)', // <-- Kolom baru
                'waktu_titip' => Carbon::now()->subHours(2),
                'waktu_selesai' => null,
                'nama_penerima' => null,
                'status' => 'belum selesai',
                'foto' => 'foto_barang/sample_paket.jpg',
                'catatan' => 'Box Coklat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'nama_barang' => 'Makanan (GoFood)',
                'nama_penitip' => 'Driver Gojek', // <-- Kolom baru
                'tujuan' => 'Arkan (Kamar 305)', // <-- Kolom baru
                'waktu_titip' => Carbon::today()->addHours(10),
                'waktu_selesai' => Carbon::today()->addHours(10)->addMinutes(15),
                'nama_penerima' => 'Arkan',
                'status' => 'selesai',
                'foto' => null,
                'catatan' => 'Ayam Geprek',
                'created_at' => Carbon::today()->addHours(10),
                'updated_at' => Carbon::today()->addHours(10)->addMinutes(15),
            ],
        ]);
    }
}