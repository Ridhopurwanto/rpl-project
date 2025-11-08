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
        Schema::create('tamu', function (Blueprint $table) {
            $table->bigIncrements('id_tamu');
            $table->string('nama_tamu');
            $table->string('instansi');
            $table->string('tujuan');
            $table->unsignedBigInteger('id_pengguna')->index('tamu_id_pengguna_foreign');
            $table->dateTime('waktu_datang');
            $table->dateTime('waktu_pulang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tamu');
    }
};
