<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::create('patroli_claims', function (Blueprint $table) {
            $table->bigIncrements('id_claim');
            $table->unsignedBigInteger('id_pengguna')->index('patroli_claims_id_pengguna_foreign');
            $table->unsignedBigInteger('id_shift')->nullable()->index('patroli_claims_id_shift_foreign');
            $table->unsignedBigInteger('id_patroli_rule')->nullable()->index('patroli_id_patroli_rule_foreign_idx');
            $table->date('tanggal');
            $table->timestamp('claimed_at')->useCurrent();
        });
    }

     
    public function down(): void
    {
        Schema::dropIfExists('patroli_claims');
    }
};
