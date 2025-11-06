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
        // Sesuai diagram: SHIFT
        Schema::create('shift', function (Blueprint $table) {
            $table->id('id_shift'); // Primary Key: id_shift
            $table->unsignedBigInteger('id_pengguna'); // Foreign Key: id_pengguna
            $table->date('tanggal');
            $table->enum('jenis_shift', ['Pagi', 'Malam', 'Off']); // Sesuai petunjuk
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
        Schema::dropIfExists('shift');
    }
};