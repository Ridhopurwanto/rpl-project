<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\User; // <-- DIPERBAIKI: Menggunakan model User bawaan
use Carbon\Carbon;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Kosongkan tabel terlebih dahulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Barang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 2. Cari satu pengguna 'anggota' untuk dijadikan pencatat
        $anggota = User::where('peran', 'anggota')->first(); // <-- DIPERBAIKI

        // 3. Jika tidak ada anggota, buat satu
        if (!$anggota) {
            $this->command->info('Tidak ada pengguna "anggota" ditemukan, membuat satu...');
            
            $anggota = User::factory()->create([ // <-- DIPERBAIKI
                'nama_lengkap' => 'Anggota Seeder',
                'username' => 'anggota_seeder',
                'password' => bcrypt('password'),
                'peran' => 'anggota'
            ]);
        }
        
        // Asumsi PK Anda adalah 'id_pengguna'
        $anggotaId = $anggota->id_pengguna; 

        // 4. Siapkan data dummy
        $dataBarang = [
            [
                'kategori' => 'titipan',
                'id_pengguna' => $anggotaId,
                'nama_barang' => 'Paket Shopee',
                'lokasi_penemuan' => null,
                'nama_pelapor' => 'J&T Express',
                'waktu_lapor' => Carbon::now()->subHours(2),
                'waktu_selesai' => null,
                'nama_penerima' => null,
                'status' => 'belum selesai',
                'foto' => 'foto_barang/sample_paket.jpg',
                'catatan' => 'Box Coklat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kategori' => 'temuan',
                'id_pengguna' => $anggotaId,
                'nama_barang' => 'Kunci Motor',
                'lokasi_penemuan' => 'Maskam',
                'nama_pelapor' => 'Zidan',
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
                'kategori' => 'titipan',
                'id_pengguna' => $anggotaId,
                'nama_barang' => 'Makanan (GoFood)',
                'lokasi_penemuan' => null,
                'nama_pelapor' => 'Driver Gojek',
                'waktu_lapor' => Carbon::today()->addHours(10), // Jam 10 pagi
                'waktu_selesai' => Carbon::today()->addHours(10)->addMinutes(15),
                'nama_penerima' => 'Arkan',
                'status' => 'selesai',
                'foto' => null,
                'catatan' => 'Ayam Geprek',
                'created_at' => Carbon::today()->addHours(10),
                'updated_at' => Carbon::today()->addHours(10)->addMinutes(15),
            ],
            [
                'kategori' => 'temuan',
                'id_pengguna' => $anggotaId,
                'nama_barang' => 'Dompet',
                'lokasi_penemuan' => 'Kantin',
                'nama_pelapor' => 'Kessya',
                'waktu_lapor' => Carbon::yesterday()->addHours(14), // Kemarin jam 2 siang
                'waktu_selesai' => Carbon::yesterday()->addHours(16),
                'nama_penerima' => 'Rei (Pemilik)',
                'status' => 'selesai',
                'foto' => 'foto_barang/sample_dompet.jpg',
                'catatan' => 'Bahan Kulit Coklat',
                'created_at' => Carbon::yesterday()->addHours(14),
                'updated_at' => Carbon::yesterday()->addHours(16),
            ],
            [
                'kategori' => 'temuan',
                'id_pengguna' => $anggotaId,
                'nama_barang' => 'Binder',
                'lokasi_penemuan' => 'Lobby',
                'nama_pelapor' => 'Alisha',
                'waktu_lapor' => Carbon::today()->addHours(9),
                'waktu_selesai' => Carbon::today()->addHours(11),
                'nama_penerima' => 'Pemilik (Alisha)',
                'status' => 'selesai',
                'foto' => null,
                'catatan' => 'Warna Biru',
                'created_at' => Carbon::today()->addHours(9),
                'updated_at' => Carbon::today()->addHours(11),
            ],
        ];

        // 5. Masukkan data ke DB
        Barang::insert($dataBarang);

        $this->command->info('BarangSeeder berhasil dijalankan.');
    }
}