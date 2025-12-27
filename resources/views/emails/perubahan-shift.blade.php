<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perubahan Jadwal Shift</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px; 
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .email-header {
            background-color: #1e3a8a; 
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 8px 0 0;
            font-size: 13px;
            opacity: 0.8;
            font-weight: 400;
        }
        .email-body {
            padding: 40px 40px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-container img {
            height: 70px;
            width: auto;
        }
        .greeting {
            font-size: 16px;
            color: #111827;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 600;
        }
        .message-content {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 32px;
            text-align: center;
            line-height: 1.8;
        }
        .shift-details-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 30px;
            margin-bottom: 32px;
            border-left: 5px solid #1e3a8a;
        }
        .detail-row {
            display: flex;
            align-items: flex-start; 
            margin-bottom: 16px; 
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            flex: 0 0 100px; 
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        .detail-separator {
            flex: 0 0 20px;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
        .detail-value {
            flex: 1;
            font-size: 14px;
            color: #111827;
            font-weight: 600;
            text-align: left; 
        }
        .cta-button-container {
            text-align: center;
            margin-top: 10px;
        }
        .cta-button {
            display: inline-block;
            background-color: #1e3a8a;
            color: #ffffff !important; 
            font-size: 15px;
            font-weight: 600;
            padding: 14px 36px;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cta-button:hover {
            background-color: #1e40af;
            box-shadow: 0 6px 15px rgba(30, 58, 138, 0.4);
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>
                @if(isset($type) && $type == 'assignment')
                    Penentuan Jadwal Shift
                @else
                    Perubahan Jadwal Shift
                @endif
            </h1>
            <p>Sistem Informasi Keamanan (SIAP)</p>
        </div>

        <div class="email-body">
            <div class="logo-container">
                <img src="{{ $message->embed(public_path('images/logo-siap.png')) }}" alt="Logo SIAP">
            </div>

            <div class="greeting">Halo, {{ $notifiable->nama_lengkap ?? 'Anggota' }}</div>
            
            <div class="message-content">
                {{ $pesan }}
            </div>

            @if(isset($shiftData))
            <div class="shift-details-card">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 120px; padding-bottom: 12px; color: #6b7280; font-weight: 500; vertical-align: top;">Tanggal</td>
                        <td style="width: 20px; padding-bottom: 12px; color: #6b7280; text-align: center; vertical-align: top;">:</td>
                        <td style="padding-bottom: 12px; color: #111827; font-weight: 600; vertical-align: top;">{{ \Carbon\Carbon::parse($shiftData->tanggal)->translatedFormat('l, d F Y') }}</td>
                    </tr>
                    
                    @if(!isset($type) || $type != 'assignment')
                    <tr>
                        <td style="width: 120px; padding-bottom: 12px; color: #6b7280; font-weight: 500; vertical-align: top;">Shift Baru</td>
                        <td style="width: 20px; padding-bottom: 12px; color: #6b7280; text-align: center; vertical-align: top;">:</td>
                        <td style="padding-bottom: 12px; color: #1e3a8a; font-weight: 600; vertical-align: top;">{{ $shiftData->shiftRule->jenis_shift ?? '-' }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td style="width: 120px; color: #6b7280; font-weight: 500; vertical-align: top;">
                            @if(isset($type) && $type == 'assignment')
                                Status Shift
                            @else
                                Status
                            @endif
                        </td>
                        <td style="width: 20px; color: #6b7280; text-align: center; vertical-align: top;">:</td>
                        <td style="color: #111827; font-weight: 600; vertical-align: top;">
                            @if(isset($type) && $type == 'assignment')
                                Baru
                            @else
                                Diubah
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif

            <div class="cta-button-container">
                <a href="{{ route('anggota.presensi.index', ['start_date' => $shiftData->tanggal]) }}" class="cta-button">Lihat Jadwal Lengkap</a>
            </div>
        </div>

        <div class="email-footer">
            <p style="margin-bottom: 10px;">&copy; {{ date('Y') }} SIAP (Sistem Informasi Keamanan).<br>Politeknik Statistika STIS</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
