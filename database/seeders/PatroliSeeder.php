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
        DB::table('patroli')->truncate();

        DB::table('patroli')->insert([
            [
                'id_pengguna' => 2, // Anggota 1
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu_exact' => Carbon::now(),
                'wilayah' => 'Area Gedung A',
                'foto' => 'patroli/patroli_1.jpg',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jenis_patroli' => 'Patroli 1',
            ],
            [
                'id_pengguna' => 2, // Anggota 1
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu_exact' => Carbon::now()->addHours(2),
                'wilayah' => 'Area Parkir Belakang',
                'foto' => 'patroli/patroli_2.jpg',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jenis_patroli' => 'Patroli 2',
            ],
        ]);
    }
}