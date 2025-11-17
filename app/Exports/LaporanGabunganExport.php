<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// Kita gunakan FromView agar bisa pakai file Blade
// Kita gunakan ShouldAutoSize agar kolomnya rapi otomatis
class LaporanGabunganExport implements FromView, ShouldAutoSize
{
    protected $data;

    /**
     * 1. Terima data dari controller saat class ini dipanggil
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * 2. Render file Blade sebagai template Excel
     */
    public function view(): View
    {
        // Kita akan buat file ini di Langkah 4
        return view('komandan.laporan.template-excel', [
            'dataGabungan' => $this->data
        ]);
    }
}