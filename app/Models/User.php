<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Memberi tahu Laravel untuk menggunakan tabel 'pengguna'.
     */
    protected $table = 'pengguna';

    /**
     * Memberi tahu Laravel bahwa Primary Key Anda adalah 'id_pengguna'.
     */
    protected $primaryKey = 'id_pengguna';

    /**
     * Relasi ke tabel Presensi
     */
    public function presensi()
    {
        // Asumsi foreign key di tabel 'presensi' adalah 'id_pengguna'
        return $this->hasMany(Presensi::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke tabel Shift
     */
    public function shifts()
    {
        // Asumsi foreign key di tabel 'shift' adalah 'id_pengguna'
        return $this->hasMany(Shift::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke tabel Patroli
     */
    public function patroli()
    {
        return $this->hasMany(Patroli::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'jenis_shift',   // TAMBAHAN UNTUK SHIFT
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'jenis_shift' => 'integer',  // CASTING KE INTEGER
    ];

    /**
     * Accessor untuk mendapatkan nama shift dalam format string
     * 
     * @return string
     */
    public function getNamaShiftAttribute()
    {
        return $this->jenis_shift == 1 ? 'Pagi' : 'Malam';
    }

    /**
     * Scope untuk filter user berdasarkan shift
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $jenisShift
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShift($query, $jenisShift)
    {
        return $query->where('jenis_shift', $jenisShift);
    }
}