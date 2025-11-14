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
        Schema::create('patroli', function (Blueprint $table) {
            $table->bigIncrements('id_patroli');
            $table->unsignedBigInteger('id_pengguna')->index('patroli_id_pengguna_foreign');
            $table->string('nama_lengkap');
            $table->dateTime('waktu_exact');
            $table->string('wilayah');
            $table->string('foto');
            $table->date('tanggal');
            $table->timestamps();
            $table->enum('jenis_patroli', ['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patroli');
    }
};
