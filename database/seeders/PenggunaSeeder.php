<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PERHATIAN: Baris ini akan error jika dijalankan 2x
        // karena ID 1, 2, 3 sudah ada.
        // Gunakan 'php artisan migrate:fresh --seed' untuk menjalankan ini dengan aman.
        DB::table('pengguna')->insert([
            [
                'id_pengguna' => 1,
                'nama_lengkap' => 'Komandan Utama',
                'username' => 'komandan',
                'password' => Hash::make('password123'),
                'peran' => 'komandan',
                // --- Kolom Baru Ditambahkan ---
                'tanggal_lahir' => '1980-05-15',
                'no_hp' => '081234567890',
                'alamat' => 'Jln. Merdeka No. 1, Jakarta',
                'status' => 'Aktif',
                'foto_profil' => 'akun/komandan.jpg',
                // --- Batas Akhir Kolom Baru ---
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'nama_lengkap' => 'Anggota Jaga Satu',
                'username' => 'anggota1',
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                // --- Kolom Baru Ditambahkan ---
                'tanggal_lahir' => '1995-02-20',
                'no_hp' => '081211112222',
                'alamat' => 'Jln. Gatot Subroto No. 10, Jakarta',
                'status' => 'Aktif',
                'foto_profil' => 'akun/anggota.jpg',
                // --- Batas Akhir Kolom Baru ---
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 3,
                'nama_lengkap' => 'Admin BAU',
                'username' => 'bau',
                'password' => Hash::make('password123'),
                'peran' => 'bau',
                // --- Kolom Baru Ditambahkan ---
                'tanggal_lahir' => '1990-11-30',
                'no_hp' => '081233334444',
                'alamat' => 'Jln. Sudirman No. 12, Jakarta',
                'status' => 'Aktif',
                'foto_profil' => 'akun/bau.jpg',
                // --- Batas Akhir Kolom Baru ---
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}