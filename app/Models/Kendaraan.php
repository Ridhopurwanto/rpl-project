<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

 
class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    
     
    public $timestamps = true;

     
    protected $fillable = [
        'nomor_plat',
        'pemilik',
        'tipe',
    ];

     
    public function logKendaraan()
    {
        return $this->hasMany(LogKendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}