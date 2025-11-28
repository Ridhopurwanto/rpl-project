<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Profil - {{ $pengguna->nama_lengkap }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4a6fa5 0%, #2c5282 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #4a6fa5;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-photo-placeholder {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a6fa5 0%, #2c5282 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            color: white;
            font-weight: bold;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .profile-username {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .profile-role {
            display: inline-block;
            padding: 8px 20px;
            background: #4a6fa5;
            color: white;
            border-radius: 25px;
            font-size: 14px;
            text-transform: capitalize;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #4a6fa5;
            transition: all 0.3s;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-weight: 600;
        }

        .btn-primary {
            background: #4a6fa5;
            color: white;
        }

        .btn-primary:hover {
            background: #3d5a85;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 111, 165, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .btn-back {
            background: transparent;
            color: #4a6fa5;
            border: 2px solid #4a6fa5;
            padding: 8px 20px;
            font-size: 14px;
        }

        .btn-back:hover {
            background: #4a6fa5;
            color: white;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-aktif {
            background: #d4edda;
            color: #155724;
        }

        .status-tidak-aktif {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Info Profil</h1>
            <a href="{{ url('/anggota/dashboard') }}" class="btn btn-back">← Kembali</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="profile-header">
                @if($pengguna->foto_profil && file_exists(public_path('uploads/profil/' . $pengguna->foto_profil)))
                    <img src="{{ asset('uploads/profil/' . $pengguna->foto_profil) }}" alt="Foto Profil" class="profile-photo">
                @else
                    <div class="profile-photo-placeholder">
                        {{ strtoupper(substr($pengguna->nama_lengkap, 0, 1)) }}
                    </div>
                @endif
                
                <div class="profile-name">{{ $pengguna->nama_lengkap }}</div>
                <div class="profile-username">{{ '@' . $pengguna->username }}</div>
                <span class="profile-role">{{ ucfirst($pengguna->peran) }}</span>
            </div>

            <div class="info-section">
                <div class="section-title">📧 Informasi Kontak</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $pengguna->email }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. HP / WhatsApp</div>
                        <div class="info-value">{{ $pengguna->no_hp ?? '-' }}</div>
                    </div>

                    <div class="info-item full-width">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $pengguna->alamat ?? 'Belum diisi' }}</div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title">📋 Informasi Pribadi</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value">
                            @if($pengguna->tanggal_lahir)
                                {{ \Carbon\Carbon::parse($pengguna->tanggal_lahir)->format('d F Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Status Akun</div>
                        <div class="info-value">
                            <span class="status-badge {{ $pengguna->status == 'Aktif' ? 'status-aktif' : 'status-tidak-aktif' }}">
                                {{ ucfirst($pengguna->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Terdaftar Sejak</div>
                        <div class="info-value">{{ $pengguna->created_at->format('d F Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Terakhir Diperbarui</div>
                        <div class="info-value">{{ $pengguna->updated_at->format('d F Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ url('/anggota/dashboard') }}" class="btn btn-secondary">🏠 Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
