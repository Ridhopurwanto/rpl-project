<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

     
    protected $table = 'pengguna';

     
    protected $primaryKey = 'id_pengguna';

     
    public function presensi()
    {
        
        return $this->hasMany(Presensi::class, 'id_pengguna', 'id_pengguna');
    }

     
    public function shifts()
    {
        
        return $this->hasMany(Shift::class, 'id_pengguna', 'id_pengguna');
    }

     
    public function patroli()
    {
        return $this->hasMany(Patroli::class, 'id_pengguna', 'id_pengguna');
    }

     
    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'password',
        'peran',
        'jenis_jadwal',
        'tanggal_lahir', 
        'no_hp',         
        'alamat',        
        'status',        
        'foto_profil',
        'jenis_shift',   
    ];

     
    protected $hidden = [
        'password',
        'remember_token',
    ];

     
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'jenis_shift' => 'integer',  
    ];

     
    public function getNamaShiftAttribute()
    {
        return $this->jenis_shift == 1 ? 'Pagi' : 'Malam';
    }

     
    public function scopeShift($query, $jenisShift)
    {
        return $query->where('jenis_shift', $jenisShift);
    }
}