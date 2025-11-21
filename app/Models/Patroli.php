<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patroli extends Model
{
    use HasFactory;

    protected $table = 'patroli';

    protected $primaryKey = 'id_patroli';

    protected $fillable = [
        'id_pengguna',
        'nama_lengkap',
        'waktu_exact',
        'wilayah',
        'foto',
        'tanggal',
        'jenis_patroli',
    ];

    protected $casts = [
        'waktu_exact' => 'datetime',
        'tanggal' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->id_pengguna)) {
                    $model->id_pengguna = $user->id_pengguna;
                }
                if (empty($model->nama_lengkap)) {
                    $model->nama_lengkap = $user->nama_lengkap;
                }
            }
        });
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}