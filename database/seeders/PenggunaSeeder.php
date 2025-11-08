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
        // HAPUS BARIS INI:
        // DB::table('pengguna')->truncate();

        DB::table('pengguna')->insert([
            [
                'id_pengguna' => 1,
                'nama_lengkap' => 'Komandan Utama',
                'username' => 'komandan',
                'password' => Hash::make('password123'),
                'peran' => 'komandan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'nama_lengkap' => 'Anggota Jaga Satu',
                'username' => 'anggota1',
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 3,
                'nama_lengkap' => 'Admin BAU',
                'username' => 'bau',
                'password' => Hash::make('password123'),
                'peran' => 'bau',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}