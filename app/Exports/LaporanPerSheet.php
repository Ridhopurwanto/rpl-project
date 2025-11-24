<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPerSheet implements FromView, ShouldAutoSize, WithTitle, WithStyles
{
    protected $type;
    protected $data;
    protected $metadata;

    /**
     * @param string $type Jenis laporan ('presensi', 'patroli', 'barang', dll)
     * @param mixed $data Data spesifik untuk sheet ini
     * @param array $metadata Tanggal mulai dan selesai
     */
    public function __construct($type, $data, $metadata)
    {
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    // Render View Blade
    public function view(): View
    {
        return view('komandan.laporan.template-excel', [
            'sheetType' => $this->type, // Kirim jenis sheet agar view tahu tabel mana yang ditampilkan
            'data' => $this->data,      // Data spesifik sheet ini
            'meta' => $this->metadata   // Info tanggal
        ]);
    }

    // Judul Tab di Excel (Sheet Name)
    public function title(): string
    {
        return strtoupper(str_replace('_', ' ', $this->type));
    }

    // Styling Header (Bold Baris 1-5 kira-kira)
    public function styles(Worksheet $sheet)
    {
        return [
            // Style default bisa ditaruh sini jika mau
        ];
    }
}