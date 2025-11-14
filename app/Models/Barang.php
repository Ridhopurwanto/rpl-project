<?php
// app/Models/Barang.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'kategori',
        'id_pengguna',
        
        // --- PERUBAHAN DI SINI ---
        'nama_barang',      // Menggantikan 'jenis'
        'lokasi_penemuan',  // Baru
        'tujuan',           // Dipertahankan
        // --- AKHIR PERUBAHAN ---
        
        'nama_pelapor',
        'waktu_lapor',
        'waktu_selesai',
        'nama_penerima',
        'status',
        'foto',
        'catatan',
    ];

    protected static function booted()
    {
        static::saving(function ($barang) {
            // Logika pembersihan otomatis
            if ($barang->kategori === 'titip') {
                // Jika 'titip', pastikan 'lokasi_penemuan' KOSONG
                $barang->lokasi_penemuan = null;
            } elseif ($barang->kategori === 'temu') {
                // Jika 'temu', pastikan 'tujuan' KOSONG
                $barang->tujuan = null;
            }
        });
    }
    
    /**
     * Casting tipe data
     */
    protected $casts = [
        'waktu_lapor' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Relasi ke Pengguna (yang mencatat)
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}