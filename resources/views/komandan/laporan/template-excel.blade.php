<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000000; }
    </style>
</head>
<body>
    
    @php
        // --- STYLE SETUP ---
        // Style Header Kolom Data (digunakan di dalam file parts_excel masing-masing)
        $thStyle = 'font-size: 10pt; border: 1px solid #000000; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle; white-space: normal; word-wrap: break-word;';        
        
        // Style Isi Data
        $tdStyle = 'font-size: 10pt; border: 1px solid #000000; vertical-align: top; white-space: normal; word-wrap: break-word;';
        $tdCenterStyle = 'font-size: 10pt; border: 1px solid #000000; vertical-align: top; text-align: center; white-space: normal; word-wrap: break-word;';
        
        // 1. TENTUKAN TOTAL KOLOM TABEL DATA ($totalCol)
        // Ini penting agar Header & Footer bisa merge selebar tabel data
        $colspans = [
            'presensi' => 6,
            'patroli' => 7,
            'barang' => 8,
            'kendaraan' => 7,
            'tamu' => 6,
            'gangguan' => 6,
            'shift' => 34, // Fixed colspan for Shift to ensure full A4 Landscape width (3 info + 31 days)
            'anggota' => 9, 
            'kendaraan_terdaftar' => 4 
        ];
        
        // Ambil jumlah kolom sesuai tipe sheet, default 6 jika tidak ketemu
        $totalCol = $colspans[$sheetType] ?? 6;
    @endphp

    {{-- ============================================================ --}}
    {{-- HEADER KOP (FULL MERGE) --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        {{-- BARIS 1: JUDUL --}}
        <tr>
            <td colspan="{{ $totalCol }}" style="text-align: center; vertical-align: middle; font-weight: bold; font-size: 14pt; height: 30px;">
                LAPORAN KEGIATAN SECURITY
            </td>
        </tr>
        {{-- BARIS 2: PT --}}
        <tr>
            <td colspan="{{ $totalCol }}" style="text-align: center; vertical-align: middle; font-weight: bold; font-size: 12pt;">
                PT. PANCA KHARISMA UTAMA
            </td>
        </tr>
        {{-- BARIS 3: UNIT (BORDER BOTTOM DISINI) --}}
        <tr>
            <td colspan="{{ $totalCol }}" style="text-align: center; vertical-align: middle; font-weight: bold; font-size: 11pt; padding-bottom: 5px;">
                UNIT POLITEKNIK STATISTIKA STIS
            </td>
        </tr>
        {{-- BARIS 4: PERIODE --}}
        <tr>
            <td colspan="{{ $totalCol }}" style="text-align: center; vertical-align: middle; font-weight: bold; font-size: 11pt; border-bottom: 3px solid #000000; padding-top: 5px;">
                 PERIODE: {{ \Carbon\Carbon::parse($meta['tanggalMulai'])->isoFormat('D MMMM Y') }} S/D {{ \Carbon\Carbon::parse($meta['tanggalSelesai'])->isoFormat('D MMMM Y') }}
            </td>
        </tr>
    </table>
    
    {{-- Spacer Row (Jarak antara Kop dan Tabel Data) --}}
    <table>
        <tr>
            <td colspan="{{ $totalCol }}" style="height: 20px;"></td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- CONTENT DATA (MENGGUNAKAN PARTIALS) --}}
    {{-- ============================================================ --}}
    
    @if($sheetType == 'presensi')
        @include('komandan.laporan.parts_excel.presensi')
    @elseif($sheetType == 'patroli')
        @include('komandan.laporan.parts_excel.patroli')
    @elseif($sheetType == 'barang')
        @include('komandan.laporan.parts_excel.barang')
    @elseif($sheetType == 'kendaraan')
        @include('komandan.laporan.parts_excel.kendaraan')
    @elseif($sheetType == 'tamu')
        @include('komandan.laporan.parts_excel.tamu')
    @elseif($sheetType == 'gangguan')
        @include('komandan.laporan.parts_excel.gangguan')
    @elseif($sheetType == 'shift')
        @include('komandan.laporan.parts_excel.shift')
    @elseif($sheetType == 'anggota')
        @include('komandan.laporan.parts_excel.anggota')
    @elseif($sheetType == 'kendaraan_terdaftar')
        @include('komandan.laporan.parts_excel.kendaraan_terdaftar')
    @endif

</body>
</html>