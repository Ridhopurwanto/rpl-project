<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'status',
        'tanggal',
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * Event 'creating' ini berjalan SEBELUM data disimpan ke database.
         * $model adalah data presensi yang AKAN dibuat.
         */
        static::creating(function ($model) {
            
            // Cek apakah ada pengguna yang sedang login
            if (Auth::check()) {
                
                // 1. Isi 'id_pengguna' secara otomatis dari user yang login
                //    (jika belum diisi)
                if (empty($model->id_pengguna)) {
                    $model->id_pengguna = Auth::user()->id_pengguna;
                }
                
                // 2. Isi 'nama_lengkap' secara otomatis DARI user yang login
                if (empty($model->nama_lengkap)) {
                    $model->nama_lengkap = Auth::user()->nama_lengkap;
                }
            }
        });
    }
    
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