<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailOnly extends Notification
{
    use Queueable;

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        // Signed verification URL valid for 24 hours — used for resend flow only.
        // Does not include credentials since those were already sent in the welcome email.
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verify Your Email — VSULHS_SSLG')
            ->greeting('Hello, ' . $notifiable->full_name . '!')
            ->line('You requested a new verification link.')
            ->action('Verify Email', $verificationUrl)
            ->line('This link expires in 24 hours.')
            ->line('If you did not request this, please ignore this email.');
    }
}