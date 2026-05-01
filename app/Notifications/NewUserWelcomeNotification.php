<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class NewUserWelcomeNotification extends Notification
{
    use Queueable;

    protected string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        // Generate a signed verification URL valid for 24 hours.
        // This embeds the verify link directly in the welcome email so
        // only ONE email is ever sent when a member account is created.
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Welcome to VSULHS_SSLG — Verify Your Email')
            ->greeting('Hello, ' . $notifiable->full_name . '!')
            ->line('Your account has been created successfully.')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Temporary Password:** ' . $this->password)
            ->action('Verify Email & Login', $verificationUrl)
            ->line('Please verify your email first, then log in and change your password.')
            ->line('This verification link expires in 24 hours.');
    }
}