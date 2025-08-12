<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerEmailOtp extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $otp,
        protected string $purpose // 'verify' | 'login'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->purpose === 'verify' ? 'Verify Your Email - OTP' : 'Your Login OTP';
        $line = $this->purpose === 'verify'
            ? 'Use the OTP below to verify your email address.'
            : 'Use the OTP below to log in to your customer account.';

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->line('OTP: '.$this->otp)
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this, you can safely ignore this email.');
    }
}


