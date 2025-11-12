<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel 'log_kendaraan' (Riwayat)
 * Disesuaikan dengan log_kendaraan.sql [cite: log_kendaraan.sql]
 */
class LogKendaraan extends Model
{
    use HasFactory;

    protected $table = 'log_kendaraan';
    protected $primaryKey = 'id_log';

    /**
     * Tabel ini menggunakan timestamps (created_at, updated_at)
     * [cite: log_kendaraan.sql]
     */
    public $timestamps = true;

    protected $fillable = [
        'kendaraan_id',
        'nopol',
        'pemilik',
        'tipe',
        'keterangan',
        'waktu_masuk',
        'waktu_keluar',
        'status',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'tanggal' => 'date',
    ];

    /**
     * Relasi many-to-one ke Kendaraan (Master)
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 116]
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    /**
     * Relasi many-to-one ke Pengguna (Anggota yang mencatat)
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 115]
     */
    public function pengguna()
    {
        // Ganti \App\Models\User::class dengan model Pengguna kamu jika beda
        // (Misal: \App\Models\Pengguna::class)
        return $this->belongsTo(\App\Models\User::class, 'id_pengguna', 'id_pengguna');
    }
}