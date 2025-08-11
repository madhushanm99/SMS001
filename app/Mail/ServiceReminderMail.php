<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $customerName;
    public string $vehicleNo;
    public ?string $nextServiceDate;

    public function __construct(string $customerName, string $vehicleNo, ?string $nextServiceDate)
    {
        $this->customerName = $customerName;
        $this->vehicleNo = $vehicleNo;
        $this->nextServiceDate = $nextServiceDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Reminder - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service_reminder',
            with: [
                'customerName' => $this->customerName,
                'vehicleNo' => $this->vehicleNo,
                'nextServiceDate' => $this->nextServiceDate,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


