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

     
    public function __construct($type, $data, $metadata)
    {
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    public function view(): View
    {
        return view('komandan.laporan.template-excel', [
            'sheetType' => $this->type,
            'data' => $this->data,
            'meta' => $this->metadata
        ]);
    }

    public function title(): string
    {
        return strtoupper(str_replace('_', ' ', $this->type));
    }

    public function styles(Worksheet $sheet)
    {
        return [
        ];
    }

    public function drawings()
    {
        $drawings = [];

        $drawing1 = new Drawing();
        $drawing1->setName('Logo STIS');
        $drawing1->setDescription('Logo STIS');
        $drawing1->setPath(public_path('images/stis-logo.png'));
        $drawing1->setHeight(70);
        $drawing1->setCoordinates('A1');
        $drawing1->setOffsetX(10);
        $drawing1->setOffsetY(10);
        $drawings[] = $drawing1;

        $lastColumnLetter = $this->getLastColumnLetter();
        
        $drawing2 = new Drawing();
        $drawing2->setName('Logo PKU');
        $drawing2->setDescription('Logo PKU');
        $drawing2->setPath(public_path('images/pku-logo.png'));
        $drawing2->setHeight(70);
        $coord = $lastColumnLetter . '1';

        if ($this->type == 'shift') {
             $coord = 'AF1'; 
             $offsetX = 5; 
        } else {
             $offsetX = 10;
             if ($this->type == 'barang') $offsetX = 90; 
             if ($this->type == 'kendaraan') $offsetX = 90;
             if ($this->type == 'tamu') $offsetX = 125; 
             if ($this->type == 'gangguan') $offsetX = 125; 
             if ($this->type == 'patroli') $offsetX = 60;
             if ($this->type == 'anggota') $offsetX = 25;
             if ($this->type == 'kendaraan_terdaftar') $offsetX = 90;
             if ($this->type == 'presensi') $offsetX = 60;
        }

        $drawing2->setCoordinates($coord); 
        $drawing2->setOffsetX($offsetX);
        $drawing2->setOffsetY(10);
        $drawings[] = $drawing2;

        return $drawings;
    }

    private function getLastColumnLetter()
    {
        $colCount = 6;
        
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
                 $colCount = 34;
                 break;
        }

        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
    }

    public function columnWidths(): array
    {
        if ($this->type == 'presensi') {
            return [
                'A' => 8,
                'B' => 20,
                'C' => 20,
                'D' => 45,
                'E' => 15,
                'F' => 20,
            ];
        }

        if ($this->type == 'patroli') {
            return [
                'A' => 7,
                'B' => 18,
                'C' => 15,
                'D' => 30,
                'E' => 12,
                'F' => 20,
                'G' => 20,
            ];
        }

        if ($this->type == 'barang') {
            return [
                'A' => 6,
                'B' => 22,
                'C' => 15,
                'D' => 22,
                'E' => 22,
                'F' => 15,
                'G' => 12,
                'H' => 25,
            ];
        } 

        if ($this->type == 'kendaraan') {
            return [
                'A' => 6,
                'B' => 14,
                'C' => 14,
                'D' => 14,
                'E' => 25,
                'F' => 12,
                'G' => 25,
            ];
        }

        if ($this->type == 'tamu') {
            return [
                'A' => 6,
                'B' => 15,
                'C' => 12,
                'D' => 25,
                'E' => 20,
                'F' => 30,
            ];
        }

        if ($this->type == 'gangguan') {
            return [
                'A' => 6,
                'B' => 18,
                'C' => 14,
                'D' => 20,
                'E' => 20,
                'F' => 30,
            ];
        }

        if ($this->type == 'shift') {
            $widths = [
                'A' => 4,
                'B' => 25,
                'C' => 15,
            ];
            
            $colIndex = 4;
            for ($i = 1; $i <= 31; $i++) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$letter] = 4;
                $colIndex++;
            }
            return $widths;
        }

        if ($this->type == 'kendaraan_terdaftar') {
            return [
                'A' => 5,
                'B' => 25,
                'C' => 50,
                'D' => 25,
            ];
        }

        if ($this->type == 'anggota') {
            return [
                'A' => 4,
                'B' => 8,
                'C' => 22,
                'D' => 10,
                'E' => 12,
                'F' => 12,
                'G' => 22,
                'H' => 30,
                'I' => 15,
            ];
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0); 
                
                if ($this->type == 'shift') {
                    $event->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                }

                if ($this->type == 'barang') {
                     $sheet = $event->sheet->getDelegate();
                     $highestRow = $sheet->getHighestRow();

                     $rowTemuanStart = 0;
                     $rowTitipanStart = 0;
                     
                     for ($row = 5; $row <= $highestRow; $row++) {
                        $valA = $sheet->getCell('A' . $row)->getValue();
                        
                        if ($valA === 'NO') {
                            if ($rowTemuanStart == 0) {
                                if (isset($this->data['temu']) && count($this->data['temu']) > 0) {
                                    $rowTemuanStart = $row + 1;
                                } else {
                                     if (isset($this->data['titip']) && count($this->data['titip']) > 0) {
                                        $rowTitipanStart = $row + 1;
                                     }
                                }
                            } else {
                                $rowTitipanStart = $row + 1;
                            }
                        }
                     }

                     if ($rowTemuanStart > 0 && isset($this->data['temu'])) {
                        foreach ($this->data['temu'] as $index => $item) {
                            $currentRow = $rowTemuanStart + $index;
                            
                            $sheet->getRowDimension($currentRow)->setRowHeight(150);

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
                                    $drawing->setOffsetX(10);
                                    $drawing->setOffsetY(30); 
                                    $drawing->setWorksheet($sheet);
                                }
                            } catch (\Throwable $e) {
                            }

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
                                        $drawing2->setOffsetX(10);
                                        $drawing2->setOffsetY(115); 
                                        $drawing2->setWorksheet($sheet);
                                    }
                                } catch (\Throwable $e) {
                                }
                            }
                        }
                     }

                     if ($rowTitipanStart > 0 && isset($this->data['titip'])) {
                        foreach ($this->data['titip'] as $index => $item) {
                            $currentRow = $rowTitipanStart + $index;
                            
                            $sheet->getRowDimension($currentRow)->setRowHeight(150);

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