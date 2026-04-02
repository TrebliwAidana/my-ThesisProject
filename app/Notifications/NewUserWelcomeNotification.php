<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserWelcomeNotification extends Notification
{
    use Queueable;

    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to VSULHS_SSLG')
            ->greeting('Hello ' . $notifiable->full_name)
            ->line('Your account has been created.')
            ->line('Email: ' . $notifiable->email)
            ->line('Temporary password: **' . $this->password . '**')
            ->action('Login Now', url('/login'))
            ->line('Please change your password after logging in.');
    }
}