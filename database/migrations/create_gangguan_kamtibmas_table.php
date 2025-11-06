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
        // Sesuai diagram: GANGGUAN_KAMTIBMAS
        Schema::create('gangguan_kamtibmas', function (Blueprint $table) {
            $table->id('id_gangguan'); // Primary Key: id_gangguan
            $table->unsignedBigInteger('id_pengguna'); // FK: Petugas yang melapor
            $table->dateTime('waktu_lapor');
            $table->string('lokasi');
            $table->string('foto');
            $table->text('deskripsi');
            $table->integer('jumlah');
            $table->enum('status', ['selesai', 'belum selesai']);
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
        Schema::dropIfExists('gangguan_kamtibmas');
    }
};