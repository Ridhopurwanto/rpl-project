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
        Schema::create('barang_titip', function (Blueprint $table) {
            $table->bigIncrements('id_barang');
            $table->unsignedBigInteger('id_pengguna')->index('barang_titip_id_pengguna_foreign');
            $table->string('nama_penitip');
            $table->string('nama_barang', 45)->nullable();
            $table->string('tujuan', 45)->nullable();
            $table->string('nama_penerima', 45)->nullable();
            $table->string('foto_penerima')->nullable();
            $table->enum('status', ['selesai', 'belum selesai']);
            $table->string('foto')->nullable();
            $table->text('catatan');
            $table->dateTime('waktu_titip');
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_titip');
    }
};
