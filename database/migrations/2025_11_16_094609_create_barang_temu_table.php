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
        Schema::create('barang_temu', function (Blueprint $table) {
            $table->bigIncrements('id_barang');
            $table->unsignedBigInteger('id_pengguna')->index('barang_id_pengguna_foreign');
            $table->string('nama_pelapor');
            $table->string('nama_barang');
            $table->string('lokasi_penemuan')->nullable();
            $table->enum('status', ['selesai', 'belum selesai']);
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->dateTime('waktu_lapor');
            $table->dateTime('waktu_selesai')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->string('foto_penerima')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_temu');
    }
};
