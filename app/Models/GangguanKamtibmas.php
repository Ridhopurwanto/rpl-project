<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GangguanKamtibmas extends Model
{
    use HasFactory;

    protected $table = 'gangguan_kamtibmas';
    protected $primaryKey = 'id_gangguan';

    protected $fillable = [
        'id_pengguna',
        'waktu_lapor',
        'lokasi',
        'foto',
        'deskripsi',  
        'kategori',   
        'jumlah',
    ];

    protected $casts = [
        'waktu_lapor' => 'datetime',
    ];

     
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}