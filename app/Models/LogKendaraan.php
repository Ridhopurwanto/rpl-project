<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKendaraan extends Model
{
    use HasFactory;

    protected $table = 'log_kendaraan';
    protected $primaryKey = 'id_log';
    public $timestamps = true;

     
    protected $fillable = [
        'id_kendaraan', 
        'nopol',        
        'pemilik',      
        'tipe',         
        'keterangan',   
        'waktu_masuk',  
        'waktu_keluar', 
        'status',       
        
        
    ];

     
    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        
    ];

     
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

     
    
}