<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PerubahanShiftNotification extends Notification
{
    use Queueable;

    protected $pesan;
    protected $shiftData;

    // Kita terima data pesan dan detail shift saat notifikasi dipanggil
    public function __construct($pesan, $shiftData)
    {
        $this->pesan = $pesan;
        $this->shiftData = $shiftData;
    }

    // Tentukan jalur pengiriman: Database (untuk icon lonceng) DAN Mail (email)
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // 1. Konfigurasi tampilan Email
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Pemberitahuan Perubahan Jadwal Shift')
                    ->greeting('Halo, ' . $notifiable->nama_lengkap)
                    ->line($this->pesan)
                    ->line('Tanggal: ' . $this->shiftData->tanggal)
                    ->line('Shift Baru: ' . $this->shiftData->jenis_shift)
                    ->action('Cek Jadwal', url('/jadwal')) // Ganti link sesuai route kamu
                    ->line('Terima kasih telah menggunakan aplikasi SIAP.');
    }

    // 2. Konfigurasi tampilan di Website (Database)
    public function toArray($notifiable)
    {
        return [
            'title' => 'Perubahan Shift Presensi', // Judul (Bold di gambar 1)
            'message' => $this->pesan, // Isi pesan (Text abu-abu di gambar 1)
            'shift_id' => $this->shiftData->id_shift,
            'type' => 'info', // Bisa untuk warna icon (biru/merah)
            'icon' => 'fas fa-calendar-alt', // FontAwesome icon
            'url' => url('/jadwal')
        ];
    }
}