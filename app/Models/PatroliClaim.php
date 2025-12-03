<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatroliClaim extends Model
{
    use HasFactory;

    protected $table = 'patroli_claims';
    protected $primaryKey = 'id_claim';
    
    // ✅ FIX 1: Nonaktifkan timestamps otomatis Laravel
    public $timestamps = false;
    
    protected $fillable = [
        'id_pengguna',
        'tanggal',
        'jenis_patroli',
        'claimed_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'claimed_at' => 'datetime'
    ];

    // ✅ FIX 2: Relasi ke User (sesuaikan dengan nama model user kamu)
    public function pengguna()
    {
        // Jika model user kamu bernama "User", ganti jadi User::class
        // Jika model user kamu bernama "Pengguna", tambahkan: use App\Models\Pengguna;
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}