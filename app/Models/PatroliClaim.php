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
    
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_pengguna',
        'tanggal',
        'jenis_patroli',
        'id_shift',      
        'id_patroli_rule',
        'claimed_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'claimed_at' => 'datetime'
    ];

    public function rule()
{
    
    return $this->belongsTo(PatroliRule::class, 'id_patroli_rule');
}

    public function patrolis()
    {
        
        return $this->hasMany(Patroli::class, 'id_claim');
    }

    
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift', 'id_shift');
    }

    
    public function pengguna()
    {
        
        return $this->belongsTo(user::class, 'id_pengguna', 'id_pengguna');
    }
}