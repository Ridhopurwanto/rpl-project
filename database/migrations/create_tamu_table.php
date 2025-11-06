<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamu', function (Blueprint $table) {
            $table->id('id_tamu'); // Primary Key: id_tamu
            $table->string('nama_tamu');
            $table->string('instansi');
            $table->string('tujuan');
            $table->unsignedBigInteger('id_pengguna'); // FK: Petugas yang mencatat
            $table->dateTime('waktu_datang');
            $table->dateTime('waktu_pulang')->nullable();
            $table->timestamps();

            // Relasi ke tabel pengguna
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamu');
    }
};