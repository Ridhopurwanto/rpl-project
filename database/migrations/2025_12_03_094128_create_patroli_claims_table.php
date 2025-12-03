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
        Schema::create('patroli_claims', function (Blueprint $table) {
            $table->bigIncrements('id_claim');
            $table->unsignedBigInteger('id_pengguna')->index('patroli_claims_id_pengguna_foreign');
            $table->date('tanggal');
            $table->string('jenis_patroli', 50);
            $table->timestamp('claimed_at')->useCurrent();

            $table->unique(['tanggal', 'jenis_patroli']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patroli_claims');
    }
};
