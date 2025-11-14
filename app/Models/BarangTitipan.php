<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTitipan extends Model
{
    use HasFactory;

    protected $table = 'barang_titip';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_pengguna',
        'nama_barang',
        'nama_penitip',
        'tujuan',
        'foto',
        'catatan',
        'status',
        'waktu_titip',
        'waktu_selesai',
        'nama_penerima',
        'foto_penerima',
    ];

    protected $casts = [
        'waktu_titip' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}