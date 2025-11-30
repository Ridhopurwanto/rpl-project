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
        Schema::table('log_kendaraan', function (Blueprint $table) {
            $table->foreign(['id_kendaraan'])->references(['id_kendaraan'])->on('kendaraan')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_kendaraan', function (Blueprint $table) {
            $table->dropForeign('log_kendaraan_id_kendaraan_foreign');
        });
    }
};
