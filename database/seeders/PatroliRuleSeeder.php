<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatroliRule;

class PatroliRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Shift Pagi
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 1', 'jam_mulai' => '07:30', 'jam_selesai' => '08:30'],
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 2', 'jam_mulai' => '08:30', 'jam_selesai' => '10:30'],
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 3', 'jam_mulai' => '11:30', 'jam_selesai' => '12:30'],
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 4', 'jam_mulai' => '13:40', 'jam_selesai' => '15:30'],
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 5', 'jam_mulai' => '15:30', 'jam_selesai' => '17:30'],
            ['jenis_shift' => 'Pagi', 'jenis_patroli' => 'Patroli 6', 'jam_mulai' => '17:30', 'jam_selesai' => '18:40'],
            
            // Shift Malam
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 1', 'jam_mulai' => '19:30', 'jam_selesai' => '20:20'],
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 2', 'jam_mulai' => '21:30', 'jam_selesai' => '22:30'],
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 3', 'jam_mulai' => '23:30', 'jam_selesai' => '00:30'],
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 4', 'jam_mulai' => '01:30', 'jam_selesai' => '02:30'],
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 5', 'jam_mulai' => '03:30', 'jam_selesai' => '04:30'],
            ['jenis_shift' => 'Malam', 'jenis_patroli' => 'Patroli 6', 'jam_mulai' => '05:30', 'jam_selesai' => '06:30'],
        ];

        foreach ($rules as $rule) {
            PatroliRule::create($rule);
        }
    }
}
