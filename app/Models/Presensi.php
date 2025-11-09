<?php
// app/Models/Presensi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Presensi extends Model
{
    use HasFactory;
    
    // Beri tahu Laravel nama tabel Anda jika berbeda dari 'presensis'
    protected $table = 'presensi'; 
    protected $primaryKey = 'id_presensi';

    /**
     * Properti $fillable untuk mengizinkan Mass Assignment.
     * TAMBAHKAN ARRAY INI:
     */
    protected $fillable = [
        'id_pengguna',
        'tanggal',
        'waktu_masuk',
        'foto_masuk',
        'status_masuk',
        'lokasi_masuk',
        'waktu_pulang', // Ini yang menyebabkan error
        'foto_pulang',  // Anda akan butuh ini
        'status_pulang',// Anda akan butuh ini
        'lokasi_pulang',// Anda akan butuh ini
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
     * Relasi ke model Pengguna (User)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}