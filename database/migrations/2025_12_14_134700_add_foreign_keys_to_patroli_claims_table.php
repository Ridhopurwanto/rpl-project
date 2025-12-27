<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::table('patroli_claims', function (Blueprint $table) {
            $table->foreign(['id_pengguna'])->references(['id_pengguna'])->on('pengguna')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_shift'])->references(['id_shift'])->on('shift')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_patroli_rule'], 'patroli_id_patroli_rule_foreign')->references(['id_patroli_rule'])->on('patroli_rules')->onUpdate('cascade')->onDelete('set null');
        });
    }

     
    public function down(): void
    {
        Schema::table('patroli_claims', function (Blueprint $table) {
            $table->dropForeign('patroli_claims_id_pengguna_foreign');
            $table->dropForeign('patroli_claims_id_shift_foreign');
            $table->dropForeign('patroli_id_patroli_rule_foreign');
        });
    }
};
