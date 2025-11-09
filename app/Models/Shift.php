<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    
    // Beri tahu Laravel nama tabel Anda jika berbeda dari 'presensis'
    protected $table = 'shift'; 
    protected $primaryKey = 'id_shift';

    /**
     * Relasi ke model Pengguna (User)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}
