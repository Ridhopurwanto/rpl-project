<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::create('shift_rule', function (Blueprint $table) {
            $table->increments('idshift_rule');
            $table->enum('jenis_shift', ['Pagi', 'Malam', 'Non Shift', 'Off']);
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->integer('toleransi')->nullable()->default(0);
            $table->unsignedInteger('dibuka')->nullable();
            $table->boolean('is_geotag_enabled')->nullable()->default(true);
        });
    }

     
    public function down(): void
    {
        Schema::dropIfExists('shift_rule');
    }
};
