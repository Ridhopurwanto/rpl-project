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

     
    public function sheets(): array
    {
        $sheets = [];
        
        $metadata = [
            'tanggalMulai' => $this->dataGabungan['tanggalMulai'],
            'tanggalSelesai' => $this->dataGabungan['tanggalSelesai']
        ];

        if (isset($this->dataGabungan['presensi'])) {
            $sheets[] = new LaporanPerSheet('presensi', $this->dataGabungan['presensi'], $metadata);
        }

        if (isset($this->dataGabungan['patroli'])) {
            $sheets[] = new LaporanPerSheet('patroli', $this->dataGabungan['patroli'], $metadata);
        }

        if (isset($this->dataGabungan['barang_temu']) || isset($this->dataGabungan['barang_titip'])) {
            $dataBarang = [
                'temu' => $this->dataGabungan['barang_temu'] ?? collect([]),
                'titip' => $this->dataGabungan['barang_titip'] ?? collect([])
            ];
            $sheets[] = new LaporanPerSheet('barang', $dataBarang, $metadata);
        }

        if (isset($this->dataGabungan['kendaraan'])) {
            $sheets[] = new LaporanPerSheet('kendaraan', $this->dataGabungan['kendaraan'], $metadata);
        }

        if (isset($this->dataGabungan['tamu'])) {
            $sheets[] = new LaporanPerSheet('tamu', $this->dataGabungan['tamu'], $metadata);
        }

        if (isset($this->dataGabungan['gangguan'])) {
            $sheets[] = new LaporanPerSheet('gangguan', $this->dataGabungan['gangguan'], $metadata);
        }

        if (isset($this->dataGabungan['shift'])) {
            $sheets[] = new LaporanPerSheet('shift', $this->dataGabungan['shift'], $metadata);
        }

        if (isset($this->dataGabungan['anggota'])) {
            $sheets[] = new LaporanPerSheet('anggota', $this->dataGabungan['anggota'], $metadata);
        }

        if (isset($this->dataGabungan['kendaraan_terdaftar'])) {
            $sheets[] = new LaporanPerSheet('kendaraan_terdaftar', $this->dataGabungan['kendaraan_terdaftar'], $metadata);
        }
        
        return $sheets;
    }
}