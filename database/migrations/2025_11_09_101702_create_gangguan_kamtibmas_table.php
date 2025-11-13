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
        Schema::create('gangguan_kamtibmas', function (Blueprint $table) {
            $table->bigIncrements('id_gangguan');
            $table->unsignedBigInteger('id_pengguna')->index('gangguan_kamtibmas_id_pengguna_foreign');
            $table->dateTime('waktu_lapor');
            $table->string('lokasi');
            $table->string('foto');
            $table->text('deskripsi');
            $table->enum('kategori', [
                'Unjuk Rasa',
                'Pembakaran Lahan',
                'Bentrokan Kepolisian',
                'Kriminalitas',
                'Kecelakaan',
                'Lainnya'
            ])->default('Lainnya');
            $table->timestamps();
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
