<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = true;

     
    protected $fillable = [
    'id_pengguna',
    'id_shift',
    'nama_lengkap',
    'waktu',
    'foto',
    'status',
    'jenis_presensi',
    'tanggal',
    'latitude',    
    'longitude',   
    ];


     
    protected $casts = [
        'waktu' => 'datetime', 
        'tanggal' => 'date',
    ];

     
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                if (empty($model->id_pengguna)) {
                    
                    $model->id_pengguna = Auth::user()->id_pengguna; 
                }
                if (empty($model->nama_lengkap)) {
                    $model->nama_lengkap = Auth::user()->nama_lengkap;
                }
            }
        });
    }
    
     
    public function pengguna()
    {
        
        return $this->belongsTo('App\Models\User', 'id_pengguna', 'id_pengguna');
    }

     
    public function shift()
    {
        
        
        
        return $this->belongsTo(Shift::class, 'id_shift', 'id_shift');
    }
}