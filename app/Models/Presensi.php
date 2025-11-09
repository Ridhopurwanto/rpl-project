<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * @var string
     */
    protected $table = 'presensi'; // Sesuai nama tabel

    /**
     * Primary key tabel.
     * @var string
     */
    protected $primaryKey = 'id_presensi'; // Sesuai primary key

    /**
     * Menentukan apakah model harus mencatat timestamp (created_at, updated_at).
     * @var bool
     */
    public $timestamps = true; // Sesuai di .sql

    /**
     * Kolom-kolom yang boleh diisi.
     * @var array
     */
    protected $fillable = [
        'id_pengguna',
        'nama_lengkap',
        'waktu_masuk',
        'foto_masuk',
        'waktu_pulang',
        'foto_pulang',
        'lokasi',
        'status',
        'tanggal',
    ];

    /**
     * Casting tipe data otomatis.
     * @var array
     */
    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_pulang' => 'datetime',
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke model Pengguna (jika kamu punya model Pengguna)
     * Ganti 'App\Models\Pengguna' dengan model user-mu
     */
    public function pengguna()
    {
        // Asumsi primary key di model Pengguna adalah 'id_pengguna'
        return $this->belongsTo('App\Models\Pengguna', 'id_pengguna', 'id_pengguna');
    }
}