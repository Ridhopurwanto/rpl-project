<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanGabunganExport implements WithMultipleSheets
{
    protected $dataGabungan;

    public function __construct(array $dataGabungan)
    {
        $this->dataGabungan = $dataGabungan;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Ambil metadata tanggal (untuk judul header di setiap sheet)
        $metadata = [
            'tanggalMulai' => $this->dataGabungan['tanggalMulai'],
            'tanggalSelesai' => $this->dataGabungan['tanggalSelesai']
        ];

        // 1. Sheet Presensi
        if (isset($this->dataGabungan['presensi'])) {
            $sheets[] = new LaporanPerSheet('presensi', $this->dataGabungan['presensi'], $metadata);
        }

        // 2. Sheet Patroli
        if (isset($this->dataGabungan['patroli'])) {
            $sheets[] = new LaporanPerSheet('patroli', $this->dataGabungan['patroli'], $metadata);
        }

        // 3. Sheet Barang (GABUNGAN Temu & Titip)
        // Logika: Jika user pilih salah satu atau keduanya, buat 1 sheet 'barang'
        if (isset($this->dataGabungan['barang_temu']) || isset($this->dataGabungan['barang_titip'])) {
            $dataBarang = [
                'temu' => $this->dataGabungan['barang_temu'] ?? collect([]),
                'titip' => $this->dataGabungan['barang_titip'] ?? collect([])
            ];
            $sheets[] = new LaporanPerSheet('barang', $dataBarang, $metadata);
        }

        // 4. Sheet Kendaraan
        if (isset($this->dataGabungan['kendaraan'])) {
            $sheets[] = new LaporanPerSheet('kendaraan', $this->dataGabungan['kendaraan'], $metadata);
        }

        // 5. Sheet Tamu
        if (isset($this->dataGabungan['tamu'])) {
            $sheets[] = new LaporanPerSheet('tamu', $this->dataGabungan['tamu'], $metadata);
        }

        // 6. Sheet Gangguan
        if (isset($this->dataGabungan['gangguan'])) {
            $sheets[] = new LaporanPerSheet('gangguan', $this->dataGabungan['gangguan'], $metadata);
        }

        // 7. Sheet Shift
        if (isset($this->dataGabungan['shift'])) {
            $sheets[] = new LaporanPerSheet('shift', $this->dataGabungan['shift'], $metadata);
        }

        // 8. Sheet Anggota
        if (isset($this->dataGabungan['anggota'])) {
            $sheets[] = new LaporanPerSheet('anggota', $this->dataGabungan['anggota'], $metadata);
        }

        // 9. Sheet Kendaraan Terdaftar
        if (isset($this->dataGabungan['kendaraan_terdaftar'])) {
            $sheets[] = new LaporanPerSheet('kendaraan_terdaftar', $this->dataGabungan['kendaraan_terdaftar'], $metadata);
        }
        
        return $sheets;
    }
}