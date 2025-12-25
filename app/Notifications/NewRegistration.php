<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegistration extends Notification
{
    use Queueable;
    protected $team;
    /**
     * Create a new notification instance.
     */
    public function __construct($team)
    {
        $this->team = $team;
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
    public function toMail($notifiable)
    {
        $wa = preg_replace('/[^0-9]/', '', $this->team->wa_number);
        $wa_link = str_starts_with($wa, '0')
            ? '62' . substr($wa, 1)
            : (str_starts_with($wa, '8') ? '62' . $wa : $wa);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('✅ PEMBAYARAN MASUK: ' . $this->team->name)
            ->greeting('Halo Nadiv! Ada Cuan!')
            ->line('Tim **' . $this->team->name . '** baru saja MELUNASI pendaftaran.')
            ->line('--------------------------------------------')
            ->line('Nama Tim : ' . $this->team->name)
            ->line('No. WA   : ' . $this->team->wa_number . ' (https://wa.me/' . $wa_link . ')')
            ->line('TRX ID   : ' . $this->team->trx_id)
            ->line('Nominal  : Rp ' . number_format($this->team->season->price, 0, ',', '.'))
            ->line('--------------------------------------------')
            ->action('Cek Dashboard Admin', url('/admin/dashboard'))
            ->line('--------------------------------------------')
            ->line('Mantap! Satu slot lagi sudah terisi.');
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
