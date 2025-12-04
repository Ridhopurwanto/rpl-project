<table style="width: 100%; margin-bottom: 15px; padding-bottom: 10px; border: none; border-collapse: collapse;">
    <tr style="border-bottom: 2px solid black;">
        <td style="width: 15%; text-align: center; vertical-align: middle; border: none; padding-left: 30px;">
            <img src="{{ public_path('images/stis-logo.png') }}" style="height: 85px; width: auto;">
        </td>
        <td style="width: 70%; text-align: center; vertical-align: middle; border: none;">
            <div style="font-size: 12pt; font-weight: bold; margin-bottom: 5px;">
                LAPORAN BULANAN KEGIATAN SECURITY
            </div>
            <div style="font-size: 11pt; margin-bottom: 3px;">
                PT. PANCA KHARISMA UTAMA
            </div>
            <div style="font-size: 10pt; margin-bottom: 10px;">
                OBYEK PENGAMANAN: POLITEKNIK STATISTIKA STIS JAKARTA
            </div>
            <div style="font-size: 11pt; font-weight: bold;">
                PERIODE: {{ \Carbon\Carbon::parse($tanggalMulai)->isoFormat('D MMMM Y') }} 
                S/D 
                {{ \Carbon\Carbon::parse($tanggalSelesai)->isoFormat('D MMMM Y') }}
            </div>
        </td>
        <td style="width: 15%; text-align: center; vertical-align: middle; border: none; padding-right: 30px;">
            <img src="{{ public_path('images/pku-logo.png') }}" style="height: 85px; width: auto;">
        </td>
    </tr>
</table>
