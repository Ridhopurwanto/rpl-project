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
        // Sesuai diagram: KENDARAAN
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan'); // Primary Key: id_kendaraan
            $table->string('nomor_plat')->unique();
            $table->string('pemilik');
            $table->enum('tipe', ['Roda 2', 'Roda 4']); // Sesuai petunjuk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};