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
        // Sesuai diagram: PRESENSI
        Schema::create('presensi', function (Blueprint $table) {
            $table->id('id_presensi'); // Primary Key: id_presensi
            $table->unsignedBigInteger('id_pengguna'); // Foreign Key: id_pengguna
            $table->string('nama_lengkap'); // Kolom denormalisasi sesuai diagram
            $table->dateTime('waktu_masuk');
            $table->string('foto_masuk');
            $table->dateTime('waktu_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->string('lokasi');
            $table->enum('status', ['tepat waktu', 'terlambat', 'terlalu cepat']);
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
        Schema::dropIfExists('presensi');
    }
};