<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QrisTokenExpired extends Notification
{
    use Queueable;

    protected $errorMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('[Yomuda Champs] PERINGATAN: Token GoPay QRIS Expired / Bermasalah')
                    ->greeting('Halo Admin Yomuda Championship!')
                    ->line('Sistem poller mendeteksi bahwa integrasi pembayaran GoPay QRIS bermasalah.')
                    ->line('Pesan error yang diterima dari server GoBiz:')
                    ->line('"' . $this->errorMessage . '"')
                    ->line('Karena token mati/expired, semua transaksi baru dan sync pending tidak dapat diproses secara otomatis.')
                    ->action('Perbarui Token Sekarang', route('qris.settings'))
                    ->line('Silakan login ke dashboard dan perbarui token otorisasi GoBiz Anda segera.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
