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

     
    public function __construct($pesan, $presensiData)
    {
        $this->pesan = $pesan;
        $this->presensiData = $presensiData;
    }

     
    public function via($notifiable)
    {
        return ['database', 'mail']; 
    }

     
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
