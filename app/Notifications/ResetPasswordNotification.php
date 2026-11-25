<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Token reset password.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Kata Sandi Akun Haven — RW 013 Sukamiskin')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga RW 013') . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Haven Anda di RW 013 Kelurahan Sukamiskin.')
            ->action('Atur Ulang Kata Sandi', $resetUrl)
            ->line('Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak pernah meminta pengaturan ulang kata sandi, abaikan email ini dan kata sandi Anda akan tetap aman.')
            ->salutation("Salam hangat,\nPengurus RW 013 Sukamiskin");
    }
}
