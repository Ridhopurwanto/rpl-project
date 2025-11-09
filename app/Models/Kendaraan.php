<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel master 'kendaraan'
 * Disesuaikan dengan kendaraan.sql [cite: kendaraan.sql]
 */
class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    
    /**
     * Tabel ini menggunakan timestamps (created_at, updated_at)
     * [cite: kendaraan.sql]
     */
    public $timestamps = true;

    /**
     * Kolom yang boleh diisi
     * [cite: kendaraan.sql]
     */
    protected $fillable = [
        'nomor_plat',
        'pemilik',
        'tipe',
    ];

    /**
     * Relasi one-to-many ke LogKendaraan
     * [cite: KELOMPOK3_MILETSONE2.pdf, p. 116]
     */
    public function logKendaraan()
    {
        return $this->hasMany(LogKendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}