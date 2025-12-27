<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::table('patroli', function (Blueprint $table) {
            $table->foreign(['id_claim'])->references(['id_claim'])->on('patroli_claims')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_pengguna'])->references(['id_pengguna'])->on('pengguna')->onUpdate('restrict')->onDelete('cascade');
        });
    }

     
    public function down(): void
    {
        Schema::table('patroli', function (Blueprint $table) {
            $table->dropForeign('patroli_id_claim_foreign');
            $table->dropForeign('patroli_id_pengguna_foreign');
        });
    }
};
