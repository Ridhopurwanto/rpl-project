<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRule extends Model
{
    use HasFactory;

    protected $table = 'shift_rule';
    protected $primaryKey = 'idshift_rule'; 
    public $timestamps = false; 

    protected $fillable = [
        'jenis_shift', 
        'jam_masuk',   
        'jam_keluar',  
        'toleransi',   
        'dibuka',
        'is_geotag_enabled',
    ];

    protected $casts = [
        'toleransi' => 'integer',
        'dibuka' => 'integer',
        'is_geotag_enabled' => 'boolean',
    ];

    
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'jenis_shift', 'idshift_rule');
    }
}