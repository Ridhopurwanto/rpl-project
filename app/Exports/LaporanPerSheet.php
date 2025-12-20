<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanPerSheet implements FromView, ShouldAutoSize, WithTitle, WithStyles, WithDrawings, WithColumnWidths, WithEvents
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

    public function drawings()
    {
        $drawings = [];

        // Logo Kiri (STIS) - Selalu di A1
        $drawing1 = new Drawing();
        $drawing1->setName('Logo STIS');
        $drawing1->setDescription('Logo STIS');
        $drawing1->setPath(public_path('images/stis-logo.png'));
        $drawing1->setHeight(70);
        $drawing1->setCoordinates('A1');
        $drawing1->setOffsetX(10);
        $drawing1->setOffsetY(10);
        $drawings[] = $drawing1;

        // Logo Kanan (PKU) - Di kolom terakhir
        $lastColumnLetter = $this->getLastColumnLetter();
        
        $drawing2 = new Drawing();
        $drawing2->setName('Logo PKU');
        $drawing2->setDescription('Logo PKU');
        $drawing2->setPath(public_path('images/pku-logo.png'));
        $drawing2->setHeight(70);
        $coord = $lastColumnLetter . '1';

        // Khusus Shift, karena kolom kecil-kecil, kita geser anchor ke kiri agar tidak keluar page
        if ($this->type == 'shift') {
             // Force fixed position to end of standard page (Col AI = 35 or AH = 34)
             // Anchor at AF (32), 2 cols before usage (AH=34). 
             // 3 cols * width 4 (~84px). Logo ~70px. Offset 5 puts it nicely near end.
             $coord = 'AF1'; 
             $offsetX = 5; 
        } else {
             // Logic offset biasa
             $offsetX = 10;
             if ($this->type == 'barang') $offsetX = 90; 
             if ($this->type == 'kendaraan') $offsetX = 90;
             if ($this->type == 'tamu') $offsetX = 125; 
             if ($this->type == 'gangguan') $offsetX = 125; 
             if ($this->type == 'patroli') $offsetX = 60;
             if ($this->type == 'anggota') $offsetX = 25; // Col I width 15. Offset positions logo to right with margin
             if ($this->type == 'kendaraan_terdaftar') $offsetX = 90; // Col D width 25. High offset to push to right edge.
             if ($this->type == 'presensi') $offsetX = 60; // Col F width 20.
        }

        $drawing2->setCoordinates($coord); 
        $drawing2->setOffsetX($offsetX);
        $drawing2->setOffsetY(10);
        $drawings[] = $drawing2;

        return $drawings;
    }

    private function getLastColumnLetter()
    {
        $colCount = 6; // Default
        
        switch ($this->type) {
            case 'presensi': $colCount = 6; break;
            case 'patroli': $colCount = 7; break;
            case 'barang': $colCount = 8; break;
            case 'kendaraan': $colCount = 7; break;
            case 'tamu': $colCount = 6; break;
            case 'gangguan': $colCount = 6; break;
            case 'anggota': $colCount = 9; break;
            case 'kendaraan_terdaftar': $colCount = 4; break;
            case 'shift':
                 // Fixed 34 columns (3 info + 31 days) to fill page
                 $colCount = 34;
                 break;
        }

        // Convert number to Excel Column Letter (1=A, 2=B, etc)
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
    }

    public function columnWidths(): array
    {
        if ($this->type == 'presensi') {
            return [
                'A' => 8,  // NO
                'B' => 20, // FOTO
                'C' => 20, // TANGGAL
                'D' => 45, // NAMA ANGGOTA
                'E' => 15, // WAKTU ABSEN
                'F' => 20, // STATUS
            ];
        }

        if ($this->type == 'patroli') {
            return [
                'A' => 7,  // NO
                'B' => 18, // FOTO
                'C' => 15, // TANGGAL
                'D' => 30, // PETUGAS
                'E' => 12, // WAKTU
                'F' => 20, // WILAYAH (Secukupnya judul)
                'G' => 20, // JENIS PATROLI
            ];
        }

        if ($this->type == 'barang') {
            return [
                'A' => 6,  // NO
                'B' => 22, // FOTO
                'C' => 15, // WAKTU
                'D' => 22, // NAMA BARANG
                'E' => 22, // PIHAK TERKAIT (Pelapor/Pemilik/Penitip/Penerima)
                'F' => 15, // LOKASI (Titipan = -)
                'G' => 12, // STATUS
                'H' => 25, // CATATAN
            ];
        } 

        if ($this->type == 'kendaraan') {
            return [
                'A' => 6,  // NO
                'B' => 14, // WAKTU MASUK
                'C' => 14, // WAKTU KELUAR
                'D' => 14, // NOPOL
                'E' => 25, // PEMILIK
                'F' => 12, // TIPE
                'G' => 25, // KETERANGAN
            ];
        }

        if ($this->type == 'tamu') {
            return [
                'A' => 6,  // NO
                'B' => 15, // TANGGAL
                'C' => 12, // WAKTU
                'D' => 25, // NAMA TAMU
                'E' => 20, // INSTANSI
                'F' => 30, // TUJUAN
                // Total ~108, A4 Portrait fits nicely
                'F' => 30, // TUJUAN
                // Total ~108
            ];
        }

        if ($this->type == 'gangguan') {
            return [
                'A' => 6,  // NO
                'B' => 18, // FOTO
                'C' => 14, // WAKTU
                'D' => 20, // KATEGORI
                'E' => 20, // LOKASI
                'F' => 30, // DESKRIPSI
            ];
        }

        if ($this->type == 'shift') {
            $widths = [
                'A' => 4,  // NO
                'B' => 25, // NAMA
                'C' => 15, // JABATAN
            ];
            
            // Set widths for dates up to 31 days (Total cols reach AI approx)
            $colIndex = 4; // D
            for ($i = 1; $i <= 31; $i++) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$letter] = 4;
                $colIndex++;
            }
            return $widths;
        }

        if ($this->type == 'kendaraan_terdaftar') {
            return [
                'A' => 5,  // NO
                'B' => 25, // NOMOR PLAT
                'C' => 50, // PEMILIK (Reduced from 80 to balanced width)
                'D' => 25, // TIPE
            ];
        }

        if ($this->type == 'anggota') {
            return [
                'A' => 4,  // NO
                'B' => 8,  // FOTO
                'C' => 22, // NAMA
                'D' => 10, // PERAN
                'E' => 12, // JADWAL
                'F' => 12, // TGL LAHIR
                'G' => 22, // ALAMAT
                'H' => 30, // EMAIL
                'I' => 15, // HP
            ];
        }

        // Default auto-size for others
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Konfigurasi Kertas A4
                $event->sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                
                // Konfigurasi agar pas di satu halaman (Fit to Width)
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0); 
                
                // Landscape untuk 'shift'
                if ($this->type == 'shift') {
                    $event->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                }

                // --- KHUSUS BARANG: INSERT GAMBAR MANUAL ---
                if ($this->type == 'barang') {
                     $sheet = $event->sheet->getDelegate();
                     $highestRow = $sheet->getHighestRow();

                     // Cari baris mulai untuk "BARANG TEMUAN" dan "BARANG TITIPAN"
                     // Kita cari text header tabel "NO"
                     $rowTemuanStart = 0;
                     $rowTitipanStart = 0;
                     
                     // Helper simple untuk deteksi section
                     // Asumsi: Header tabel selalu ada "NO" di kolom A
                     // Kita scanning dari baris 5 ke bawah
                     for ($row = 5; $row <= $highestRow; $row++) {
                        $valA = $sheet->getCell('A' . $row)->getValue();
                        
                        // Deteksi Header Tabel
                        if ($valA === 'NO') {
                            // Cek baris sebelumnya atau sebelumnya lagi ada judul "BARANG TEMUAN" atau "BARANG TITIPAN"?
                            // Atau cek data apa yang sedang kita proses
                            // Karena struktur dinamis, kita pakai counter saja. 
                            // Temu duluan, baru Titip.
                            if ($rowTemuanStart == 0) {
                                // Jika data temu ada, ini pasti temu
                                if (isset($this->data['temu']) && count($this->data['temu']) > 0) {
                                    $rowTemuanStart = $row + 1; // Data mulai dari row header + 1
                                } else {
                                    // Jika tidak ada temu, mungkin ini titip
                                     if (isset($this->data['titip']) && count($this->data['titip']) > 0) {
                                        $rowTitipanStart = $row + 1;
                                     }
                                }
                            } else {
                                // Jika temu sudah ketemu, maka NO berikutnya pasti titipan
                                $rowTitipanStart = $row + 1;
                            }
                        }
                     }

                     // PROSES DATA TEMUAN
                     if ($rowTemuanStart > 0 && isset($this->data['temu'])) {
                        foreach ($this->data['temu'] as $index => $item) {
                            $currentRow = $rowTemuanStart + $index;
                            
                            // SET TINGGI BARIS AGAR MUAT 2 FOTO
                            // 150 points cukup untuk 2 foto + text (lebih padat)
                            $sheet->getRowDimension($currentRow)->setRowHeight(150);

                            // 1. FOTO BARANG (Di bawah text "Barang :")
                            try {
                                $fotoPath = public_path('storage/' . $item->foto);
                                $realFotoPath = realpath($fotoPath);
                                
                                if ($item->foto && $realFotoPath && file_exists($realFotoPath) && is_file($realFotoPath)) {
                                    $drawing = new Drawing();
                                    $drawing->setName('Foto Barang');
                                    $drawing->setDescription('Foto Barang');
                                    $drawing->setPath($realFotoPath);
                                    $drawing->setHeight(50);
                                    $drawing->setCoordinates('B' . $currentRow);
                                    $drawing->setOffsetX(10); // Rata Kiri
                                    $drawing->setOffsetY(30); 
                                    $drawing->setWorksheet($sheet);
                                }
                            } catch (\Throwable $e) {
                            }

                            // 2. FOTO PENERIMA (Di bawah text "Penerima :")
                            if (strtolower($item->status ?? '') === 'selesai' && $item->foto_penerima) {
                                try {
                                    $fotoPenerimaPath = public_path('storage/' . $item->foto_penerima);
                                    $realFotoPenerimaPath = realpath($fotoPenerimaPath);
                                    
                                    if ($realFotoPenerimaPath && file_exists($realFotoPenerimaPath) && is_file($realFotoPenerimaPath)) {
                                        $drawing2 = new Drawing();
                                        $drawing2->setName('Foto Penerima');
                                        $drawing2->setDescription('Foto Penerima');
                                        $drawing2->setPath($realFotoPenerimaPath);
                                        $drawing2->setHeight(50);
                                        $drawing2->setCoordinates('B' . $currentRow);
                                        $drawing2->setOffsetX(10); // Rata Kiri
                                        $drawing2->setOffsetY(115); 
                                        $drawing2->setWorksheet($sheet);
                                    }
                                } catch (\Throwable $e) {
                                }
                            }
                        }
                     }

                     // PROSES DATA TITIPAN
                     if ($rowTitipanStart > 0 && isset($this->data['titip'])) {
                        foreach ($this->data['titip'] as $index => $item) {
                            $currentRow = $rowTitipanStart + $index;
                            
                            $sheet->getRowDimension($currentRow)->setRowHeight(150);

                            // 1. FOTO BARANG
                            try {
                                $fotoPathTitip = public_path('storage/' . $item->foto);
                                $realFotoPathTitip = realpath($fotoPathTitip);

                                if ($item->foto && $realFotoPathTitip && file_exists($realFotoPathTitip) && is_file($realFotoPathTitip)) {
                                    $drawing = new Drawing();
                                    $drawing->setName('Foto Barang');
                                    $drawing->setDescription('Foto Barang');
                                    $drawing->setPath($realFotoPathTitip);
                                    $drawing->setHeight(50);
                                    $drawing->setCoordinates('B' . $currentRow);
                                    $drawing->setOffsetX(10); 
                                    $drawing->setOffsetY(30); 
                                    $drawing->setWorksheet($sheet);
                                }
                            } catch (\Throwable $e) {
                            }

                            // 2. FOTO PENERIMA
                            if (strtolower($item->status ?? '') === 'selesai' && $item->foto_penerima) {
                                try {
                                    $fotoPenerimaPathTitip = public_path('storage/' . $item->foto_penerima);
                                    $realFotoPenerimaPathTitip = realpath($fotoPenerimaPathTitip);

                                    if ($realFotoPenerimaPathTitip && file_exists($realFotoPenerimaPathTitip) && is_file($realFotoPenerimaPathTitip)) {
                                        $drawing2 = new Drawing();
                                        $drawing2->setName('Foto Penerima');
                                        $drawing2->setDescription('Foto Penerima');
                                        $drawing2->setPath($realFotoPenerimaPathTitip);
                                        $drawing2->setHeight(50);
                                        $drawing2->setCoordinates('B' . $currentRow);
                                        $drawing2->setOffsetX(10); 
                                        $drawing2->setOffsetY(115); 
                                        $drawing2->setWorksheet($sheet);
                                    }
                                } catch (\Throwable $e) {
                                }
                            }
                        }
                     }
                }
            },
        ];
    }
}