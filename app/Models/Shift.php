<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    
    
    protected $table = 'shift'; 
    protected $primaryKey = 'id_shift';

     
    protected $fillable = [
        'id_pengguna',
        'tanggal',
        'jenis_shift',
    ];

    
    public function shiftRule()
    {
        
        return $this->belongsTo(ShiftRule::class, 'jenis_shift', 'idshift_rule');
    }

     
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}