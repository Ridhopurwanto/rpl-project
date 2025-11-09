<?php
// app/Models/Presensi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    
    // Beri tahu Laravel nama tabel Anda jika berbeda dari 'presensis'
    protected $table = 'presensi'; 
    protected $primaryKey = 'id_presensi';

    /**
     * Relasi ke model Pengguna (User)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}