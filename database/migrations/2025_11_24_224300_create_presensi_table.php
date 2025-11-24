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
        Schema::create('presensi', function (Blueprint $table) {
            $table->bigIncrements('id_presensi');
            $table->unsignedBigInteger('id_pengguna')->index('presensi_id_pengguna_foreign');
            $table->unsignedBigInteger('id_shift')->nullable()->index('presensi_id_shift_foreign');
            $table->string('nama_lengkap');
            $table->dateTime('waktu');
            $table->string('foto');
            $table->enum('status', ['tepat waktu', 'terlambat', 'terlalu cepat']);
            $table->enum('jenis_presensi', ['Masuk', 'Pulang'])->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
