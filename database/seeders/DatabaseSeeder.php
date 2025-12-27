<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\DB;     

class DatabaseSeeder extends Seeder
{
     
    public function run(): void
    {
        
        Schema::disableForeignKeyConstraints();

        
        
        
        DB::table('shift')->truncate();
        DB::table('shift_rule')->truncate();
        DB::table('presensi')->truncate();
        DB::table('patroli')->truncate();
        DB::table('log_kendaraan')->truncate(); 
        DB::table('tamu')->truncate();
        DB::table('barang_temu')->truncate();
        DB::table('barang_titip')->truncate();
        DB::table('gangguan_kamtibmas')->truncate();

        
        
        DB::table('pengguna')->truncate();
        DB::table('kendaraan')->truncate();

        
        Schema::enableForeignKeyConstraints();

        
        $this->call([
            PenggunaSeeder::class,
            KendaraanSeeder::class,
            ShiftRuleSeeder::class,
            ShiftSeeder::class,
            PresensiSeeder::class,
            PatroliSeeder::class,
            LogKendaraanSeeder::class,
            TamuSeeder::class,
            BarangTemuSeeder::class,
            BarangTitipSeeder::class,
            GangguanKamtibmasSeeder::class,
            NotificationSeeder::class,
            PatroliRuleSeeder::class,
            DailyLogSeeder::class
        ]);
    }
}