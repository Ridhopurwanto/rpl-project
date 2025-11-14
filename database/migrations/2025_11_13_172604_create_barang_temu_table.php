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
            $table->string('nama_pelapor');
            $table->unsignedBigInteger('id_pengguna')->index('barang_temu_id_pengguna_foreign');
            $table->dateTime('waktu_lapor');
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['selesai', 'belum selesai']);
            $table->string('foto');
            $table->text('catatan');
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
