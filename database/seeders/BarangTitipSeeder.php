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
                'nama_penitip' => 'Kurir AnterAja',
                'id_pengguna' => 2, // Diterima Anggota 1
                'waktu_titip' => Carbon::now()->subMinutes(30),
                'waktu_selesai' => null,
                'status' => 'belum selesai',
                'foto' => 'titipan/paket_1.jpg',
                'catatan' => 'Paket untuk Bp. Komandan, ditaruh di pos jaga',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}