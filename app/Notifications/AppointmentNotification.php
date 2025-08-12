<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;
    public $type;
    public $message;
    public $title;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, string $type, string $title, string $message)
    {
        $this->appointment = $appointment;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line($this->message)
                    ->action('View Appointment', route('appointments.show', $this->appointment))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'appointment_no' => $this->appointment->appointment_no,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'customer_name' => $this->appointment->customer->name ?? 'Unknown',
            'vehicle_no' => $this->appointment->vehicle_no,
            'service_type' => $this->appointment->service_type,
            'appointment_date' => $this->appointment->appointment_date,
            'appointment_time' => $this->appointment->appointment_time,
            'status' => $this->appointment->status,
            'icon' => $this->getIconClass(),
            'color' => $this->getColorClass(),
            'url' => route('appointments.show', $this->appointment),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Get the icon class based on notification type.
     */
    private function getIconClass(): string
    {
        return match($this->type) {
            'appointment_created' => 'bi bi-info-circle',
            'appointment_confirmed' => 'bi bi-check-circle',
            'appointment_rejected' => 'bi bi-x-circle',
            'appointment_cancelled' => 'bi bi-dash-circle',
            'appointment_completed' => 'bi bi-check-square',
            default => 'bi bi-info-circle',
        };
    }

    /**
     * Get the color class based on notification type.
     */
    private function getColorClass(): string
    {
        return match($this->type) {
            'appointment_created' => 'text-success',
            'appointment_confirmed' => 'text-success',
            'appointment_rejected' => 'text-danger',
            'appointment_cancelled' => 'text-warning',
            'appointment_completed' => 'text-info',
            default => 'text-success',
        };
    }
}
