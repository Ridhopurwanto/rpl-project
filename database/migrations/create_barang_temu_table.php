<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_temu', function (Blueprint $table) {
            $table->id('id_barang'); // Primary Key: id_barang
            $table->string('nama_pelapor');
            $table->unsignedBigInteger('id_pengguna'); // FK: Petugas yang mencatat
            $table->dateTime('waktu_lapor');
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['selesai', 'belum selesai']);
            $table->string('foto');
            $table->text('catatan');
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
        Schema::dropIfExists('barang_temu');
    }
};