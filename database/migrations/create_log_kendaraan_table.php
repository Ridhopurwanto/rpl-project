<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_kendaraan', function (Blueprint $table) {
            $table->id('id_log'); // Primary Key: id_log
            $table->unsignedBigInteger('id_kendaraan'); // Foreign Key: id_kendaraan
            $table->unsignedBigInteger('id_pengguna'); // Foreign Key: id_pengguna
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->enum('keterangan', ['menginap', 'tidak menginap'])->nullable();
            $table->date('tanggal');
            $table->timestamps();

            // Relasi ke tabel kendaraan
            $table->foreign('id_kendaraan')
                  ->references('id_kendaraan')
                  ->on('kendaraan')
                  ->onDelete('cascade');
            
            // Relasi ke tabel pengguna
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_kendaraan');
    }
};