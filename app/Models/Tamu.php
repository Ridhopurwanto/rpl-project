<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    use HasFactory;

    
    protected $table = 'tamu';
    protected $primaryKey = 'id_tamu';

    protected $fillable = [
        'nama_tamu',
        'instansi',
        'tujuan',
        'id_pengguna',
        'waktu_datang',
        'no_identitas',
    ];

    protected $casts = [
        'waktu_datang' => 'datetime',
    ];

     
    public function pengguna()
    {
        
        
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}