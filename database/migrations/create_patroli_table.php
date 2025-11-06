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
        // Sesuai diagram: PATROLI
        Schema::create('patroli', function (Blueprint $table) {
            $table->id('id_patroli'); // Primary Key: id_patroli
            $table->unsignedBigInteger('id_pengguna'); // Foreign Key: id_pengguna
            $table->string('nama_lengkap'); // Kolom denormalisasi sesuai diagram
            $table->dateTime('waktu_exact');
            $table->dateTime('waktu_patroli');
            $table->string('wilayah');
            $table->string('foto');
            $table->date('tanggal');
            $table->timestamps();

            // Relasi ke tabel penggunas
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patroli');
    }
};