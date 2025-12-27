<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->bigIncrements('id_pengguna');
            $table->string('nama_lengkap');
            $table->string('username')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->rememberToken();
            $table->enum('peran', ['anggota', 'komandan', 'supervisor']);
            $table->enum('jenis_jadwal', ['shift', 'non_shift'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->string('foto_profil')->nullable();
            $table->timestamps();
        });
    }

     
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
