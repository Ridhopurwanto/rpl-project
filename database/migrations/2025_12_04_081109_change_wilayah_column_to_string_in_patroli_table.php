<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patroli', function (Blueprint $table) {
            // Ubah kolom wilayah dari ENUM ke VARCHAR(255)
            $table->string('wilayah', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patroli', function (Blueprint $table) {
            // Kembalikan ke ENUM jika rollback (opsional)
            $table->enum('wilayah', [
                'AREA POS 2',
                'LOBBY VVIP', 
                'LOBBY AUDIT',
                'KANTIN',
                'Area Gedung A',
                'Area Parkir Belakang',
                'Area Pos-2',
                'Area BAU',
                'Area Kantin',
                'Area BAAM',
                'Akses Lorong GD-3',
                'Akses Lorong GD-2',
                'Area Pos-3',
                'Akses Besi GD-2',
                'Akses Kaca GD-2',
                'Akses Selatan Audit',
                'Akses Ruang Lektor',
                'Akses Parkir Basement',
                'Akses Lift GD-2',
            ])->change();
        });
    }
};