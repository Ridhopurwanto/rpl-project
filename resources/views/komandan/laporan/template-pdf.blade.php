<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #cccccc;
            font-weight: bold;
            text-align: center;
        }

        h2 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1a1a1a;
            font-size: 13pt;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .header-utama {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
        }

        .footer-col {
            width: 30%;
            text-align: center;
        }

        .footer-col p {
            margin: 5px 0;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 100%;
        }

        .shift-P { background-color: #ffff00 !important; }
        .shift-M { background-color: #4fc3f7 !important; }
        .shift-O { background-color: #ff5252 !important; color: white !important; }
        .shift-N { background-color: #e0e0e0 !important; }
    </style>
</head>
<body>

    {{-- HEADER HALAMAN --}}
    @include('komandan.laporan.parts.pdf-header')

    {{-- KONTEN LAPORAN --}}

    {{-- PRESENSI --}}
    @if(isset($presensi) && count($presensi) > 0)
        @include('komandan.laporan.parts.pdf-presensi')
        <div class="page-break"></div>
    @endif

    {{-- PATROLI --}}
    @if(isset($patroli) && count($patroli) > 0)
        @include('komandan.laporan.parts.pdf-patroli')
        <div class="page-break"></div>
    @endif

    {{-- BARANG --}}
    @if((isset($barang_temu) && count($barang_temu) > 0) || (isset($barang_titip) && count($barang_titip) > 0))
        @include('komandan.laporan.parts.pdf-barang')
        <div class="page-break"></div>
    @endif

    {{-- KENDARAAN --}}
    @if(isset($kendaraan) && count($kendaraan) > 0)
        @include('komandan.laporan.parts.pdf-kendaraan')
        <div class="page-break"></div>
    @endif

    {{-- TAMU --}}
    @if(isset($tamu) && count($tamu) > 0)
        @include('komandan.laporan.parts.pdf-tamu')
        <div class="page-break"></div>
    @endif

    {{-- GANGGUAN --}}
    @if(isset($gangguan) && count($gangguan) > 0)
        @include('komandan.laporan.parts.pdf-gangguan')
        <div class="page-break"></div>
    @endif

    {{-- SHIFT --}}
    @if(isset($shift) && count($shift) > 0)
        @include('komandan.laporan.parts.pdf-shift')
    @endif

    {{-- FOOTER SIGNATURE --}}
    <div class="footer" style="margin-top: 60px;">
        <div class="footer-col">
            <p><strong>DIBUAT OLEH,</strong></p>
            <div class="signature-line" style="height: 50px;"></div>
            <p style="margin-top: 20px;">Petugas</p>
        </div>
        <div class="footer-col">
            <p><strong>MENGETAHUI,</strong></p>
            <div class="signature-line" style="height: 50px;"></div>
            <p style="margin-top: 20px;">Kepala Keamanan</p>
        </div>
    </div>

</body>
</html>
