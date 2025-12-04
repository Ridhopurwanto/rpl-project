<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PerubahanStatusPresensiNotification extends Notification
{
    use Queueable;

    protected $pesan;
    protected $presensiData;

    /**
     * Create a new notification instance.
     *
     * @param string $pesan
     * @param object $presensiData
     */
    public function __construct($pesan, $presensiData)
    {
        $this->pesan = $pesan;
        $this->presensiData = $presensiData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Kirim ke DB dan Email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pemberitahuan Perubahan Status Presensi')
            ->view('emails.perubahan-status-presensi', [
                'pesan' => $this->pesan,
                'presensiData' => $this->presensiData,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Perubahan Status Presensi',
            'message' => $this->pesan,
            'type' => 'status_change',
            'presensi_id' => $this->presensiData->id_presensi,
        ];
    }
}
