<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'id_pengguna'   => 1,
                'nama_lengkap'  => 'Komandan Utama',
                'username'      => 'komandan',
                'email'         => 'komandan@siap.com',
                'password'      => Hash::make('password123'),
                'peran'         => 'komandan',
                'jenis_jadwal'  => 'non_shift',
                'tanggal_lahir' => '1980-05-15',
                'no_hp'         => '081234567890',
                'alamat'        => 'Jln. Merdeka No. 1, Jakarta',
                'status'        => 'Aktif',
                'foto_profil'   => 'akun/komandan.jpg',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            [
                'id_pengguna'   => 2,
                'nama_lengkap'  => 'Anggota Jaga Satu',
                'username'      => 'anggota1',
                'email'         => 'anggota1@siap.com',
                'password'      => Hash::make('password123'),
                'peran'         => 'anggota',
                'jenis_jadwal'  => 'shift',
                'tanggal_lahir' => '1995-02-20',
                'no_hp'         => '081211112222',
                'alamat'        => 'Jln. Gatot Subroto No. 10, Jakarta',
                'status'        => 'Aktif',
                'foto_profil'   => 'akun/anggota.jpg',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            [
                'id_pengguna'   => 3,
                'nama_lengkap'  => 'Admin BAU',
                'username'      => 'bau',
                'email'         => 'admin_bau@siap.com',
                'password'      => Hash::make('password123'),
                'peran'         => 'bau',
                'jenis_jadwal'  => null,   // admin BAU tidak pakai jadwal shift
                'tanggal_lahir' => '1990-11-30',
                'no_hp'         => '081233334444',
                'alamat'        => 'Jln. Sudirman No. 12, Jakarta',
                'status'        => 'Aktif',
                'foto_profil'   => 'akun/bau.jpg',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
        ]);
    }
}
