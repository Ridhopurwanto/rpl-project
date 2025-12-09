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
        Schema::table('patroli', function (Blueprint $table) {
            // Tambah kolom id_shift setelah id_pengguna (sesuaikan dengan struktur tabelmu)
            $table->unsignedBigInteger('id_shift')->nullable()->after('id_pengguna');

            // Foreign key ke tabel shift (kolom primary key: id_shift)
            $table->foreign('id_shift')
                ->references('id_shift')
                ->on('shift')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patroli', function (Blueprint $table) {
            // Hapus constraint dulu, baru kolomnya
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
        });
    }
};
