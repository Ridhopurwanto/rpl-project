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
    protected $type; // 'assignment' or 'change'

    public function __construct($pesan, $shiftData = null, $type = 'change')
    {
        $this->pesan = $pesan;
        $this->shiftData = $shiftData;
        $this->type = $type;
    }

    // Kirim via database DAN email
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // Konfigurasi Email
    public function toMail($notifiable)
    {
        $subject = ($this->type == 'assignment') 
            ? 'Pemberitahuan Penentuan Jadwal Shift' 
            : 'Pemberitahuan Perubahan Jadwal Shift';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.perubahan-shift', [
                'pesan' => $this->pesan,
                'shiftData' => $this->shiftData,
                'notifiable' => $notifiable,
                'type' => $this->type,
            ]);
    }

    // Konfigurasi Database (untuk lonceng)
    public function toArray($notifiable)
    {
        $jenisShift = 'Tidak Diketahui';
        if ($this->shiftData && $this->shiftData->shiftRule) {
            $jenisShift = $this->shiftData->shiftRule->jenis_shift;
        }

        return [
            'title' => 'Perubahan Shift',
            'message' => $this->pesan,
            'shift_id' => $this->shiftData->id_shift ?? null,
            'tanggal' => $this->shiftData->tanggal ?? null,
            'jenis_shift' => $jenisShift,
        ];
    }
}