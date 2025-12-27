<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

     
    protected $fillable = [
        'kategori',
        'id_pengguna',
        
        
        'nama_barang',      
        'lokasi_penemuan',  
        'tujuan',           
        
        
        'nama_pelapor',
        'waktu_lapor',
        'waktu_selesai',
        'nama_penerima',
        'status',
        'foto',
        'catatan',
    ];

    protected static function booted()
    {
        static::saving(function ($barang) {
            
            if ($barang->kategori === 'titip') {
                
                $barang->lokasi_penemuan = null;
            } elseif ($barang->kategori === 'temu') {
                
                $barang->tujuan = null;
            }
        });
    }
    
     
    protected $casts = [
        'waktu_lapor' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

     
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}