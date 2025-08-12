<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerWelcomeCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $name,
        protected string $email,
        protected string $plainPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = 'http://localhost:8000/customer/login?email=' . urlencode($this->email) . '&temp=' . urlencode($this->plainPassword);

        return (new MailMessage)
            ->subject('Your Customer Portal Account Credentials')
            ->greeting('Hello '.$this->name.',')
            ->line('An account has been created for you on our customer portal.')
            ->line('Login email: '.$this->email)
            ->line('Temporary password: '.$this->plainPassword)
            ->line('For security, you must verify your email using the OTP we sent and then change this temporary password immediately after logging in.')
            ->action('Open Customer Portal', $loginUrl)
            ->line('If you did not request this, please contact us.');
    }
}


