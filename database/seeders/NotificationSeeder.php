<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Seeding dummy 'notifications' for Anggota Jaga Satu (ID: 2)...\n";

        $userId = 2; // ID Anggota Jaga Satu
        $notifiableType = 'App\\Models\\User'; // Sesuai model yang kamu gunakan
        $now = Carbon::now();
        $past = Carbon::now()->subHours(2);

        $notificationsData = [
            // Notifikasi 1: Perubahan Shift (BELUM DIBACA)
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\PerubahanShiftNotification', // Class Notifikasi yang kita buat sebelumnya
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'title' => 'Perubahan Shift Presensi',
                    'message' => 'Shift Anda untuk tanggal 2025-11-25 diubah menjadi shift Malam.',
                    'shift_id' => 4,
                    'type' => 'info',
                    'icon' => 'fas fa-calendar-alt',
                ]),
                'read_at' => null, // null = Belum Dibaca
                'created_at' => $now->subMinutes(5),
                'updated_at' => $now->subMinutes(5),
            ],
            
            // Notifikasi 2: Laporan Diterima (BELUM DIBACA)
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\PerubahanShiftNotification',
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'title' => 'Laporan Diterima',
                    'message' => 'Laporan gangguan lampu mati di Gedung B sudah ditindaklanjuti.',
                    'gangguan_id' => 2,
                    'type' => 'success',
                    'icon' => 'fas fa-check',
                ]),
                'read_at' => null, // null = Belum Dibaca
                'created_at' => $past->subMinutes(30),
                'updated_at' => $past->subMinutes(30),
            ],
            
            // Notifikasi 3: Pengumuman (SUDAH DIBACA)
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\PerubahanShiftNotification',
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'title' => 'Pengumuman Penting',
                    'message' => 'Ada briefing wajib di Pos 1 pukul 15:00 hari ini.',
                    'type' => 'warning',
                    'icon' => 'fas fa-exclamation-triangle',
                ]),
                'read_at' => $past->subHour(1), // Ada tanggal = Sudah Dibaca
                'created_at' => $past->subHour(1)->subMinutes(15),
                'updated_at' => $past->subHour(1)->subMinutes(15),
            ],
        ];

        DB::table('notifications')->insert($notificationsData);

        echo "Notification seeding complete.\n";
    }
}