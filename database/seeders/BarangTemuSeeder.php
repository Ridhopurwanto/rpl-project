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
                'id_pengguna' => 2,
                'nama_barang' => 'Kunci Motor',
                'nama_pelapor' => 'Zidan',
                'lokasi_penemuan' => 'Maskam',
                'waktu_lapor' => Carbon::now()->subMinutes(30),
                'waktu_selesai' => null,
                'nama_penerima' => null,
                'status' => 'belum selesai',
                'foto' => 'foto_barang/sample_kunci.jpg',
                'catatan' => 'Gantungan Kunci Kucing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'nama_barang' => 'Dompet',
                'nama_pelapor' => 'Kessya',
                'lokasi_penemuan' => 'Kantin',
                'waktu_lapor' => Carbon::yesterday()->addHours(14),
                'waktu_selesai' => Carbon::yesterday()->addHours(16),
                'nama_penerima' => 'Rei (Pemilik)',
                'status' => 'selesai',
                'foto' => 'foto_barang/sample_dompet.jpg',
                'catatan' => 'Bahan Kulit Coklat',
                'created_at' => Carbon::yesterday()->addHours(14),
                'updated_at' => Carbon::yesterday()->addHours(16),
            ],
            [
                'id_pengguna' => 2,
                'nama_barang' => 'Binder',
                'nama_pelapor' => 'Alisha',
                'lokasi_penemuan' => 'Lobby',
                'waktu_lapor' => Carbon::today()->addHours(9),
                'waktu_selesai' => Carbon::today()->addHours(11),
                'nama_penerima' => 'Pemilik (Alisha)',
                'status' => 'selesai',
                'foto' => null,
                'catatan' => 'Warna Biru',
                'created_at' => Carbon::today()->addHours(9),
                'updated_at' => Carbon::today()->addHours(11),
            ],
        ]);
    }
}