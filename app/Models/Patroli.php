<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patroli extends Model
{
    use HasFactory;

    protected $table = 'patroli';

    protected $primaryKey = 'id_patroli';

    /**
     * Mengatur kolom mana yang boleh diisi.
     */
    protected $fillable = [
        'id_pengguna',
        'nama_lengkap',
        'waktu_exact',
        'waktu_patroli',
        'wilayah',
        'foto',
        'tanggal',
    ];

    protected $casts = [
        'waktu_exact' => 'datetime',
        'tanggal' => 'date',
    ];
}