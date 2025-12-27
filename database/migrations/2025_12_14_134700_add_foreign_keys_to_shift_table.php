<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::table('shift', function (Blueprint $table) {
            $table->foreign(['id_pengguna'])->references(['id_pengguna'])->on('pengguna')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['jenis_shift'], 'shift_id_shiftrule_foreign')->references(['idshift_rule'])->on('shift_rule')->onUpdate('cascade')->onDelete('no action');
        });
    }

     
    public function down(): void
    {
        Schema::table('shift', function (Blueprint $table) {
            $table->dropForeign('shift_id_pengguna_foreign');
            $table->dropForeign('shift_id_shiftrule_foreign');
        });
    }
};
