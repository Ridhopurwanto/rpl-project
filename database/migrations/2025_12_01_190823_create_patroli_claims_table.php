<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patroli_claims', function (Blueprint $table) {
            $table->id('id_claim');
            $table->unsignedBigInteger('id_pengguna');
            $table->date('tanggal');
            $table->string('jenis_patroli', 50);
            $table->timestamp('claimed_at')->useCurrent();
            
            // Unique constraint: 1 patroli per hari hanya 1 orang
            $table->unique(['tanggal', 'jenis_patroli']);
            
            // Foreign key
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patroli_claims');
    }
};