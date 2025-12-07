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
        Schema::create('patroli_rules', function (Blueprint $table) {
            $table->id('id_patroli_rule');
            $table->enum('jenis_shift', ['Pagi', 'Malam'])->comment('Shift Pagi atau Malam');
            $table->enum('jenis_patroli', ['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            // Unique constraint: satu jenis_patroli hanya bisa ada satu pengaturan per shift
            $table->unique(['jenis_shift', 'jenis_patroli'], 'unique_shift_patroli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patroli_rules');
    }
};
