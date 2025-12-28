# Sistem Informasi Manajemen Keamanan

Sistem berbasis web yang dibangun untuk memodernisasi dan mendigitalkan operasional keamanan (Satpam). Aplikasi ini membantu pencatatan, pemantauan, dan pelaporan aktivitas keamanan sehari-hari secara real-time dan terstruktur.

## Fitur Utama

Aplikasi ini memiliki 3 modul utama berdasarkan peran pengguna:

### 1. Anggota (Security Guard)
Modul untuk petugas di lapangan melakukan input data operasional.
- **Presensi**: Check-in dan Check-out shift kerja.
- **Patroli Digital**: Melakukan patroli dengan scan checkpoint area dan pelaporan kondisi (Aman/Ada Temuan) beserta foto bukti.
- **Manajemen Kendaraan**: Mencatat keluar-masuk kendaraan (Log Kendaraan) dan mendata kendaraan penghuni/tetap.
- **Buku Tamu**: Mencatat data tamu yang berkunjung.
- **Barang Temuan & Titipan**: Mencatat barang yang ditemukan atau dititipkan di pos penjagaan.
- **Laporan Gangguan**: Melaporkan kejadian atau gangguan Kamtibmas.

### 2. Komandan (Head of Security)
Modul untuk pengawasan dan validasi.
- **Dashboard Monitoring**: Melihat ringkasan aktivitas harian.
- **Manajemen Personil**: Mengatur jadwal shift dan plotting anggota.
- **Validasi Data**: Memantau dan memvalidasi laporan dari anggota (Presensi, Patroli, dll).
- **Laporan & Unduhan**: Mengunduh rekapitulasi laporan dalam format PDF/Excel untuk arsip atau pelaporan ke atasan.

### 3. Supervisor
Modul untuk administrasi tingkat atas.
- **Monitoring Keseluruhan**: Akses 'View-Only' atau administratif ke seluruh data keamanan.
- **Rekapitulasi Laporan**: Akses ke laporan manajerial.

## Teknologi

Project ini dibangun menggunakan:
- [Laravel](https://laravel.com)
- Database MySQL
- Styling dengan [Tailwind CSS](https://tailwindcss.com) (via Vite)
- Authentication & Authorization standart Laravel

## Instalasi

Clone repository dan jalankan perintah berikut untuk instalasi lokal:

```bash
# Install PHP dependencies
composer install

# Copy configuration
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Configure your .env database settings, then run migrations (and seeders if available)
php artisan migrate --seed

# Install JS dependencies & Build assets
npm install
npm run build

# Run local server
php artisan serve
```

## Akun Demo (Default Seeder)

Jika menggunakan seeder bawaan, berikut adalah akun default yang dapat digunakan untuk login:
- **Anggota**  
      Username : anggota1  
      Password : password123
- **Komandan**  
      Username : komandan  
      Password : password123
- **Supervisor**  
      Username : bau  
      Password : password123

## Dokumen Pelengkap

Dokumen seperti Laporan, Alih hak sistem, dan BAST dapat dilihat melalui link berikut:...  
Atau dapat dilihat melalui folder Dokumen Pelengkap pada repository ini.

  
