<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTemuan extends Model
{
    use HasFactory;

    protected $table = 'barang_temu';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_pengguna',
        'nama_barang',
        'nama_pelapor',
        'lokasi_penemuan',
        'foto',
        'catatan',
        'status',
        'waktu_lapor',
        'waktu_selesai',
        'nama_penerima',
        'foto_penerima',
    ];

    protected $casts = [
        'waktu_lapor' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}