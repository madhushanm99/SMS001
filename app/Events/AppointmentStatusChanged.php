<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;
    public $previousStatus;
    public $newStatus;
    public $handledBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, ?string $previousStatus, string $newStatus, ?string $handledBy = null)
    {
        $this->appointment = $appointment;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->handledBy = $handledBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('appointments'),
            new Channel('staff-notifications'),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'appointment.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'appointment_no' => $this->appointment->appointment_no,
            'customer_name' => $this->appointment->customer->name ?? 'Unknown',
            'vehicle_no' => $this->appointment->vehicle_no,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'handled_by' => $this->handledBy,
            'appointment_date' => $this->appointment->appointment_date->format('M d, Y'),
            'appointment_time' => $this->appointment->getFormattedTime(),
            'timestamp' => now()->toISOString(),
        ];
    }
}
