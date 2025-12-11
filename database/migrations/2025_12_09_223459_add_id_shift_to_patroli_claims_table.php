<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patroli_claims', function (Blueprint $table) {
            $table->unsignedBigInteger('id_shift')->nullable()->after('id_pengguna');
            $table->foreign('id_shift')
                ->references('id_shift')->on('shift')  // ← id_shift ke id_shift
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('patroli_claims', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
        });
    }
};