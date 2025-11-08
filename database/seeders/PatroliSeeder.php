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
                'waktu_patroli' => Carbon::now()->setHour(10)->setMinute(0),
                'wilayah' => 'Area Gedung A',
                'foto' => 'patroli/patroli_1.jpg',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2, // Anggota 1
                'nama_lengkap' => 'Anggota Jaga Satu',
                'waktu_exact' => Carbon::now()->addHours(2),
                'waktu_patroli' => Carbon::now()->setHour(12)->setMinute(0),
                'wilayah' => 'Area Parkir Belakang',
                'foto' => 'patroli/patroli_2.jpg',
                'tanggal' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}