<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PatroliClaim extends Model
{
    use HasFactory;

    protected $table = 'patroli_claims';
    protected $primaryKey = 'id_claim';
    
    // ✅ Nonaktifkan timestamps otomatis Laravel
    public $timestamps = false;
    
    protected $fillable = [
        'id_pengguna',
        'tanggal',
        'jenis_patroli',
        'id_shift',      // ✅ TAMBAHKAN INI! (PENTING!)
        'claimed_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'claimed_at' => 'datetime'
    ];

    // ✅ Relasi ke Shift
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift', 'id_shift');
    }

    // ✅ Relasi ke User/Pengguna
    public function pengguna()
    {
        // Ganti 'User' jadi 'Pengguna' kalau model kamu namanya Pengguna
        return $this->belongsTo(user::class, 'id_pengguna', 'id_pengguna');
    }
}