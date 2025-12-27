<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->bigIncrements('id_shift');
            $table->unsignedBigInteger('id_pengguna')->index('shift_id_pengguna_foreign');
            $table->date('tanggal');
            $table->unsignedInteger('jenis_shift')->index('shift_id_shiftrule_foreign_idx');
            $table->timestamps();
        });
    }

     
    public function down(): void
    {
        Schema::dropIfExists('shift');
    }
};
