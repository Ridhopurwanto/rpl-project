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
        Schema::create('shift', function (Blueprint $table) {
            $table->bigIncrements('id_shift');
            $table->unsignedBigInteger('id_pengguna')->index('shift_id_pengguna_foreign');
            $table->date('tanggal');
            $table->enum('jenis_shift', ['Pagi', 'Malam', 'Off']);
            $table->timestamps();
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
