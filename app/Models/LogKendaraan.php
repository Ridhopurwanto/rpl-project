<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKendaraan extends Model
{
    use HasFactory;

    protected $table = 'log_kendaraan';
    protected $primaryKey = 'id_log';
    public $timestamps = true;

    /**
     * PERBAIKAN:
     * Menyesuaikan $fillable agar sesuai dengan database Anda
     */
    protected $fillable = [
        'id_kendaraan', // Ada di DB
        'nopol',        // Ada di DB
        'pemilik',      // Ada di DB
        'tipe',         // Ada di DB (meskipun tidak terlihat di screenshot)
        'keterangan',   // Ada di DB
        'waktu_masuk',  // Ada di DB
        'waktu_keluar', // Ada di DB
        'status',       // Ada di DB
        // 'id_pengguna' Dihapus (tidak ada di DB)
        // 'tanggal' Dihapus (tidak ada di DB)
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        // 'tanggal' Dihapus
    ];

    /**
     * Relasi many-to-one ke Kendaraan (Master)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    /**
     * Relasi pengguna() DIHAPUS karena
     * kolom 'id_pengguna' tidak ada di database Anda
     */
    // public function pengguna() { ... }
}