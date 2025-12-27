<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::table('barang_titip', function (Blueprint $table) {
            $table->foreign(['id_pengguna'])->references(['id_pengguna'])->on('pengguna')->onUpdate('restrict')->onDelete('cascade');
        });
    }

     
    public function down(): void
    {
        Schema::table('barang_titip', function (Blueprint $table) {
            $table->dropForeign('barang_titip_id_pengguna_foreign');
        });
    }
};
