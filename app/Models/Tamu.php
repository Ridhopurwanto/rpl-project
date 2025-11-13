<?php
// app/Models/Tamu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    use HasFactory;

    // Sesuaikan dengan skema tabel Anda
    protected $table = 'tamu';
    protected $primaryKey = 'id_tamu';

    protected $fillable = [
        'nama_tamu',
        'instansi',
        'tujuan',
        'id_pengguna',
        'waktu_datang',
        'waktu_pulang',
    ];

    protected $casts = [
        'waktu_datang' => 'datetime',
    ];

    /**
     * Relasi ke Pengguna (yang mencatat)
     */
    public function pengguna()
    {
        // Asumsi model Pengguna Anda adalah App\Models\Pengguna
        // Ganti jika nama modelnya beda (misal: App\Models\User)
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}