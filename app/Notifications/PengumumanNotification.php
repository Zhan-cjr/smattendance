<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengumumanNotification extends Notification
{
    use Queueable;

    private $pengumuman;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengumuman Baru: ' . $this->pengumuman->judul,
            'message' => substr(strip_tags($this->pengumuman->isi), 0, 100) . '...',
            'url' => route('pengumuman.index'), // Or show page if it exists for users
            'type' => 'pengumuman',
            'icon' => 'ti-bell'
        ];
    }
}
