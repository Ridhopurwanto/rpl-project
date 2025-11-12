<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ..._create_log_kendaraans_table.php
    public function up()
    {
        Schema::create('log_kendaraan', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            
            // Link ke tabel master, BISA NULL (jika tidak terdaftar)
            $table->unsignedBigInteger('id_kendaraan')->nullable();
            
            // Data duplikat (tetap dicatat meski tidak ada di master)
            $table->string('nopol');
            $table->string('pemilik');
            $table->string('tipe');
            
            // Keterangan yang bisa diedit (sesuai permintaan Anda)
            $table->string('keterangan'); // "Menginap" atau "Tidak Menginap"
            
            // Waktu & Status
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->string('status')->default('Masuk'); // "Masuk" atau "Keluar"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_kendaraan');
    }
};
