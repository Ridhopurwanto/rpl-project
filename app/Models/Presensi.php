<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = true;

    /**
     * Kolom-kolom yang boleh diisi.
     * DISESUAIKAN DENGAN DATABASE ANDA (image_a1eb42.png)
     */
    protected $fillable = [
        'id_pengguna',
        'id_shift', // <-- PENTING
        'nama_lengkap',
        'waktu', // <-- DULU 'waktu_masuk'
        'foto', // <-- DULU 'foto_masuk'
        'status',
        'jenis_presensi', // <-- PENTING
        'tanggal',
    ];

    /**
     * Casting tipe data otomatis.
     * INI ADALAH PERBAIKAN UTAMA UNTUK ERROR ANDA
     */
    protected $casts = [
        'waktu' => 'datetime', // <-- DULU 'waktu_masuk' & 'waktu_pulang'
        'tanggal' => 'date',
    ];

    /**
     * Event 'creating' untuk mengisi data otomatis.
     * (Kode 'boot' Anda sudah bagus, saya hanya merapikannya)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                if (empty($model->id_pengguna)) {
                    // Pastikan model User Anda punya 'id_pengguna'
                    $model->id_pengguna = Auth::user()->id_pengguna; 
                }
                if (empty($model->nama_lengkap)) {
                    $model->nama_lengkap = Auth::user()->nama_lengkap;
                }
            }
        });
    }
    
    /**
     * Relasi ke model Pengguna (Asumsi Anda punya model User/Pengguna)
     */
    public function pengguna()
    {
        // Sesuaikan 'App\Models\User' jika nama model Anda beda
        return $this->belongsTo('App\Models\User', 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke model Shift (PENTING UNTUK FILTER)
     */
    public function shift()
    {
        // Menghubungkan ke Model Shift
        // Foreign key di 'presensi' adalah 'id_shift'
        // Primary key di 'shift' adalah 'id_shift'
        return $this->belongsTo(Shift::class, 'id_shift', 'id_shift');
    }
}