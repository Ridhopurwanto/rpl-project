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
        @if((isset($patroli) && count($patroli) > 0) || (isset($barang_temu) && count($barang_temu) > 0) || (isset($barang_titip) && count($barang_titip) > 0) || (isset($kendaraan) && count($kendaraan) > 0) || (isset($tamu) && count($tamu) > 0) || (isset($gangguan) && count($gangguan) > 0) || (isset($shift) && count($shift) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- PATROLI --}}
    @if(isset($patroli) && count($patroli) > 0)
        @include('komandan.laporan.parts.pdf-patroli')
        @if((isset($barang_temu) && count($barang_temu) > 0) || (isset($barang_titip) && count($barang_titip) > 0) || (isset($kendaraan) && count($kendaraan) > 0) || (isset($tamu) && count($tamu) > 0) || (isset($gangguan) && count($gangguan) > 0) || (isset($shift) && count($shift) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- BARANG --}}
    @if((isset($barang_temu) && count($barang_temu) > 0) || (isset($barang_titip) && count($barang_titip) > 0))
        @include('komandan.laporan.parts.pdf-barang')
        @if((isset($kendaraan) && count($kendaraan) > 0) || (isset($tamu) && count($tamu) > 0) || (isset($gangguan) && count($gangguan) > 0) || (isset($shift) && count($shift) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- KENDARAAN --}}
    @if(isset($kendaraan) && count($kendaraan) > 0)
        @include('komandan.laporan.parts.pdf-kendaraan')
        @if((isset($tamu) && count($tamu) > 0) || (isset($gangguan) && count($gangguan) > 0) || (isset($shift) && count($shift) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- TAMU --}}
    @if(isset($tamu) && count($tamu) > 0)
        @include('komandan.laporan.parts.pdf-tamu')
        @if((isset($gangguan) && count($gangguan) > 0) || (isset($shift) && count($shift) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- GANGGUAN --}}
    @if(isset($gangguan) && count($gangguan) > 0)
        @include('komandan.laporan.parts.pdf-gangguan')
        @if(isset($shift) && count($shift) > 0)
            <div class="page-break"></div>
        @endif
    @endif

    {{-- SHIFT --}}
    @if(isset($shift) && count($shift) > 0)
        @include('komandan.laporan.parts.pdf-shift')
        @if((isset($anggota) && count($anggota) > 0) || (isset($kendaraan_terdaftar) && count($kendaraan_terdaftar) > 0))
            <div class="page-break"></div>
        @endif
    @endif

    {{-- ANGGOTA --}}
    @if(isset($anggota) && count($anggota) > 0)
        @include('komandan.laporan.parts.pdf-anggota')
        @if(isset($kendaraan_terdaftar) && count($kendaraan_terdaftar) > 0)
            <div class="page-break"></div>
        @endif
    @endif

    {{-- KENDARAAN TERDAFTAR --}}
    @if(isset($kendaraan_terdaftar) && count($kendaraan_terdaftar) > 0)
        @include('komandan.laporan.parts.pdf-kendaraan-terdaftar')
    @endif

    {{-- FOOTER SIGNATURE --}}
    <table style="width: 95%; margin-top: 60px; border: none;">
        <tr style="border: none;">
            <td style="width: 33%; text-align: center; vertical-align: top; border: none;">
                <p style="margin-bottom: 100px;"><strong>DIBUAT OLEH,</strong></p>
                <div style="display: inline-block; text-align: center;">
                    <div style="border-top: 1px solid #000; width: 150px; margin-bottom: 3px;"></div>
                    <div style="margin: 0; padding: 0; line-height: 1.5;">KEPALA KEAMANAN</div>
                </div>
            </td>
            <td style="width: 34%; text-align: center; vertical-align: top; border: none;">
                <p style="margin-bottom: 100px;"><strong>DIKETAHUI OLEH,</strong></p>
                <div style="display: inline-block; text-align: center;">
                    <div style="border-top: 1px solid #000; width: 200px; margin-bottom: 3px;"></div>
                    <div style="margin: 0; padding: 0; line-height: 1.5;">PANCA KHARISMA UTAMA</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
