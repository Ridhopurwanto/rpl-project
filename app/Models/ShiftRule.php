<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRule extends Model
{
    use HasFactory;

    protected $table = 'shift_rule';
    protected $primaryKey = 'idshift_rule'; // Sesuai gambar 3
    public $timestamps = false; // Biasanya tabel master tidak butuh timestamps

    protected $fillable = [
        'jenis_shift', // Enum('Pagi','Malam', dll)
        'jam_masuk',   // Time
        'jam_keluar',  // Time
        'toleransi',   // Int (Menit)
        'dibuka',
        'is_geotag_enabled',
    ];

    // Relasi ke tabel 'shift' (One Rule has Many Shifts)
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'jenis_shift', 'idshift_rule');
    }
}