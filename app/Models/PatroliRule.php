<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatroliRule extends Model
{
    use HasFactory;

    protected $table = 'patroli_rules';
    protected $primaryKey = 'id_patroli_rule';

    protected $fillable = [
        'jenis_shift',
        'jenis_patroli',
        'jam_mulai',
        'jam_selesai',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    /**
     * Mendapatkan jam mulai dalam format string (HH:mm)
     */
    public function getJamMulaiAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    /**
     * Mendapatkan jam selesai dalam format string (HH:mm)
     */
    public function getJamSelesaiAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }
}
