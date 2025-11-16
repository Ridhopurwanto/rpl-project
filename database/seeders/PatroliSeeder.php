<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatroliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan truncate() DIHAPUS dari sini
        // 1. Ambil ID pengguna (sesuai PenggunaSeeder)
        $anggota = DB::table('pengguna')->where('username', 'anggota1')->first();

        // 2. Cek jika data ada untuk menghindari error
        if (!$anggota) {
            $this->command->error('PenggunaSeeder belum dijalankan. Melewati PatroliSeeder.');
            return;
        }

        // Data patroli (sesuai screenshot dan migrasi enum Anda)
        DB::table('patroli')->insert([
            [
                'id_pengguna' => $anggota->id_pengguna,
                'nama_lengkap' => $anggota->nama_lengkap,
                'waktu_exact' => '2025-11-16 09:25:00',
                
                // Menggunakan string ENUM, bukan ID
                'wilayah' => 'Area Pos 1', 
                
                'foto' => 'patroli/patroli_1.jpg',
                'tanggal' => '2025-11-16',
                
                'jenis_patroli' => 'Patroli 1', 
                
                'created_at' => '2025-11-16 09:25:00',
                'updated_at' => '2025-11-16 09:25:00',
            ],
            [
                'id_pengguna' => $anggota->id_pengguna,
                'nama_lengkap' => $anggota->nama_lengkap,
                'waktu_exact' => '2025-11-16 11:25:00',

                // Menggunakan string ENUM, bukan ID
                'wilayah' => 'Lobby VVIP',
                
                'foto' => 'patroli/patroli_2.jpg',
                'tanggal' => '2025-11-16',
                'jenis_patroli' => 'Patroli 2',
                'created_at' => '2025-11-16 11:25:00', // Sesuai screenshot
                'updated_at' => '2025-11-16 11:25:00', // Sesuai screenshot
            ],
             [
                'id_pengguna' => $anggota->id_pengguna,
                'nama_lengkap' => $anggota->nama_lengkap,
                'waktu_exact' => Carbon::now(),

                // Menggunakan string ENUM, bukan ID
                'wilayah' => 'Area BAU',
                
                'foto' => 'patroli/patroli_3.jpg',
                'tanggal' => Carbon::today(),
                'jenis_patroli' => 'Patroli 3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}