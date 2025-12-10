<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Patroli;
use App\Models\Tamu;
use App\Models\BarangTitipan;
use App\Models\BarangTemuan;
use App\Models\GangguanKamtibmas;

class DailyLogSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // STRICTLY 2025 as requested
        $startDate = Carbon::create(2025, 10, 1);
        $endDate = Carbon::create(2025, 12, 31);
        
        $anggotaUsers = User::where('peran', 'anggota')->get();
        
        if ($anggotaUsers->isEmpty()) {
            $this->command->warn('No Anggota users found. Using all users.');
            $anggotaUsers = User::all();
        }
        
        $userIds = $anggotaUsers->pluck('id_pengguna')->toArray();

        // === DATASETS (INDONESIAN CONTEXT) ===
        $barangList = [
            'Laptop ASUS', 'Laptop Lenovo', 'MacBook Air', 'HP Samsung', 'iPhone 13', 
            'Kunci Motor Honda', 'Kunci Mobil Toyota', 'Dompet Kulit Coklat', 'Dompet Hitam',
            'Tas Ransel Eiger', 'Tas Selempang', 'Helm KYT', 'Helm INK', 'Jaket Denim',
            'Tumblr Starbucks', 'Botol Minum Tupperware', 'Flashdisk Sandisk', 'Kacamata Hitam'
        ];

        $lokasiList = [
            'Lobby Utama', 'Pos Satpam Depan', 'Parkiran Dosen', 'Parkiran Mahasiswa',
            'Kantin Pusat', 'Musholla', 'Gedung 1 Lantai 1', 'Gedung 2 Lantai 3',
            'Perpustakaan', 'Ruang Server', 'Toilet Lantai 1', 'Taman Depan'
        ];

        $catatanTemuan = [
            'Ditemukan tertinggal di atas meja.', 'Ketemu di bawah kursi tunggu.', 
            'Diserahkan oleh mahasiswa.', 'Tertinggal di toilet.', 'Ditemukan saat patroli.',
            'Tergeletak di area parkir.'
        ];

        $catatanTitipan = [
            'Dititipkan untuk diambil nanti sore.', 'Titipan paket untuk Pak Budi.',
            'Barang milik tamu yang tertinggal sementara.', 'Menunggu dijemput gojek.',
            'Disimpan di pos sementara.'
        ];

        $tujuanTamu = [
            'Bertemu Kepala Bagian', 'Mengantar Paket Dokumen', 'Maintenance Jaringan',
            'Tamu Rektorat', 'Mengambil Ijazah', 'Konsultasi Layanan', 'Survey Lokasi',
            'Rapat Koordinasi', 'Service AC'
        ];

        $instansiList = [
            'PT Telkom Indonesia', 'PLN Area setempat', 'Dinas Pendidikan', 'Polda Metro Jaya',
            'CV Maju Mundur', 'JNE Express', 'J&T Cargo', 'Gojek Driver', 'Grab Express',
            'Wali Murid', 'Alumni Angkatan 2020', 'PT Bersih Sejahtera'
        ];

        $gangguanKategori = [
            'Mabok' => ['Orang mabuk tidur di depan gerbang.', 'Pemuda mabuk membuat keributan kecil.'],
            'Perkelahian' => ['Cekcok antar pengemudi ojol.', 'Perdebatan sengit di parkiran.'],
            'Laka Lantas' => ['Senggolan motor diparkiran.', 'Mobil mundur menabrak pot bunga.'],
            'Curat' => ['Laporan kehilangan helm di parkiran luar.'],
            'Kebakaran' => ['Korsleting listrik kecil di pos jaga.', 'Tempat sampah terbakar puntung rokok.']
        ];


        $this->command->info('Starting REALISTIC INDONESIAN daily seeding from ' . $startDate->toDateString() . ' to ' . $endDate->toDateString());

        while ($startDate->lte($endDate)) {
            $dateString = $startDate->toDateString();
            $this->command->info("Processing date: " . $dateString);

            // === 1. PRESENSI LOGIC ===
            $dailyUsers = $faker->randomElements($userIds, rand(3, 5));

            foreach ($dailyUsers as $uid) {
                $user = $anggotaUsers->firstWhere('id_pengguna', $uid);
                $isPagi = $faker->boolean(70); 
                $shiftId = $isPagi ? 1 : 2; 
                $shiftStart = $isPagi ? '07:00:00' : '19:00:00';
                $shiftEnd   = $isPagi ? '19:00:00' : '07:00:00'; 

                // -- MASUK --
                $entryTime = $startDate->copy()->setTimeFromTimeString($shiftStart);
                $entryOffset = rand(-20, 30); 
                $entryTime->addMinutes($entryOffset);
                
                $statusMasuk = $entryOffset > 10 ? 'terlambat' : 'tepat waktu';

                Presensi::create([
                    'id_pengguna' => $uid,
                    'id_shift' => $shiftId,
                    'nama_lengkap' => $user->nama_lengkap ?? $faker->name,
                    'waktu' => $entryTime,
                    'foto' => 'placeholder.jpg',
                    'status' => $statusMasuk,
                    'jenis_presensi' => 'Masuk',
                    'tanggal' => $dateString,
                    'latitude' => -7.29 + ($faker->randomFloat(5, -0.001, 0.001)),
                    'longitude' => 112.73 + ($faker->randomFloat(5, -0.001, 0.001)),
                ]);

                // -- PULANG --
                if ($faker->boolean(95)) {
                    $exitTime = $startDate->copy()->setTimeFromTimeString($shiftEnd);
                    if (!$isPagi) $exitTime->addDay(); 
                    
                    $exitOffset = rand(-15, 60);
                    $exitTime->addMinutes($exitOffset);

                    $statusPulang = $exitOffset < 0 ? 'terlalu cepat' : 'tepat waktu'; 

                    Presensi::create([
                        'id_pengguna' => $uid,
                        'id_shift' => $shiftId,
                        'nama_lengkap' => $user->nama_lengkap ?? $faker->name,
                        'waktu' => $exitTime,
                        'foto' => 'placeholder.jpg',
                        'status' => $statusPulang,
                        'jenis_presensi' => 'Pulang',
                        'tanggal' => $dateString, 
                        'latitude' => -7.29 + ($faker->randomFloat(5, -0.001, 0.001)),
                        'longitude' => 112.73 + ($faker->randomFloat(5, -0.001, 0.001)),
                    ]);
                }
            }

            // === 2. PATROLI LOGIC ===
            $patroliCount = rand(1, 2);
            for ($p = 0; $p < $patroliCount; $p++) {
                $patrollerId = $faker->randomElement($userIds);
                $patroller = $anggotaUsers->firstWhere('id_pengguna', $patrollerId);
                $sessionStart = $startDate->copy()->setTime(rand(8, 22), rand(0, 59));
                $jenisPatroli = 'Patroli ' . rand(1, 6); 

                for ($area = 1; $area <= 17; $area++) {
                    $checkpointTime = $sessionStart->copy()->addMinutes($area * rand(1, 3));
                    
                    Patroli::create([
                        'id_pengguna' => $patrollerId,
                        'nama_lengkap' => $patroller->nama_lengkap ?? $faker->name,
                        'waktu_exact' => $checkpointTime,
                        'wilayah' => 'Area ' . $area,
                        'foto' => 'placeholder.jpg',
                        'tanggal' => $dateString,
                        'jenis_patroli' => $jenisPatroli,
                    ]);
                }
            }

            // === 3. BARANG LOGIC ===
            $barangCount = rand(1, 3);
            for ($b = 0; $b < $barangCount; $b++) {
                $ownerId = $faker->randomElement($userIds);
                $itemName = $faker->randomElement($barangList);
                
                if ($faker->boolean(50)) {
                    // TITIPAN
                    $startT = $startDate->copy()->setTime(rand(7, 10), rand(0,59));
                    $isDone = $faker->boolean(80);
                    $endT = $isDone ? $startT->copy()->addHours(rand(1, 8)) : null;

                    BarangTitipan::create([
                        'id_pengguna' => $ownerId,
                        'nama_barang' => $itemName,
                        'nama_penitip' => $faker->name,
                        'tujuan' => $faker->name,
                        'foto' => 'placeholder.jpg',
                        'catatan' => $faker->randomElement($catatanTitipan),
                        'status' => $isDone ? 'selesai' : 'belum selesai',
                        'waktu_titip' => $startT,
                        'waktu_selesai' => $endT,
                        'nama_penerima' => $isDone ? $faker->name : null,
                        'foto_penerima' => $isDone ? 'placeholder.jpg' : null,
                    ]);
                } else {
                    // TEMUAN
                    $startT = $startDate->copy()->setTime(rand(7, 16), rand(0,59));
                    $isDone = $faker->boolean(60);
                     $endT = $isDone ? $startT->copy()->addHours(rand(1, 48)) : null;

                    BarangTemuan::create([
                        'id_pengguna' => $ownerId,
                        'nama_barang' => $itemName,
                        'nama_pelapor' => $faker->name,
                        'lokasi_penemuan' => $faker->randomElement($lokasiList),
                        'foto' => 'placeholder.jpg',
                        'catatan' => $faker->randomElement($catatanTemuan),
                        'status' => $isDone ? 'selesai' : 'belum selesai',
                        'waktu_lapor' => $startT,
                        'waktu_selesai' => $endT,
                        'nama_penerima' => $isDone ? $faker->name : null,
                        'foto_penerima' => $isDone ? 'placeholder.jpg' : null,
                    ]);
                }
            }

            // === 4. TAMU LOGIC ===
            $tamuCount = rand(2, 5);
            for ($t = 0; $t < $tamuCount; $t++) {
                Tamu::create([
                    'nama_tamu' => $faker->name,
                    'instansi' => $faker->randomElement($instansiList),
                    'tujuan' => $faker->randomElement($tujuanTamu),
                    'id_pengguna' => $faker->randomElement($userIds),
                    'waktu_datang' => $startDate->copy()->setTime(rand(8, 15), rand(0, 59)),
                    'no_identitas' => $faker->numerify('35##############'), // NIK Jatim-ish style
                ]);
            }

            // === 5. GANGGUAN ===
            if ($faker->boolean(20)) { 
                $kat = $faker->randomElement(array_keys($gangguanKategori));
                $desc = $faker->randomElement($gangguanKategori[$kat]);
                
                GangguanKamtibmas::create([
                    'id_pengguna' => $faker->randomElement($userIds),
                    'waktu_lapor' => $startDate->copy()->setTime(rand(0, 23), rand(0, 59)),
                    'lokasi' => $faker->randomElement($lokasiList),
                    'foto' => 'placeholder.jpg',
                    'deskripsi' => $desc,
                    'kategori' => $kat,
                ]);
            }

            $startDate->addDay();
        }

        $this->command->info('Daily seeding 2025 completed!');
    }
}
