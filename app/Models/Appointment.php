<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_no',
        'customer_id',
        'vehicle_no',
        'service_type',
        'appointment_date',
        'appointment_time',
        'status',
        'customer_notes',
        'staff_notes',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        // Keep time as-is; we'll format safely in accessors to avoid double-date strings
        'appointment_time' => 'datetime',
        'handled_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_no', 'vehicle_no');
    }

    // Generate appointment number
    public static function generateAppointmentNo(): string
    {
        $lastAppointment = self::latest('id')->first();
        $number = $lastAppointment ? ((int) substr($lastAppointment->appointment_no, 3)) + 1 : 1;
        return 'APT' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Service type methods
    public function getServiceTypeLabel(): string
    {
        return match($this->service_type) {
            'NS' => 'Normal Service',
            'FS' => 'Full Service',
            default => 'Unknown Service'
        };
    }

    public function getServiceTypeColor(): string
    {
        return match($this->service_type) {
            'NS' => 'primary',
            'FS' => 'success',
            default => 'secondary'
        };
    }

    // Status methods
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger',
            'completed' => 'info',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'confirmed' => 'Confirmed',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    // Appointment date and time methods
    public function getFormattedDateTime(): string
    {
        return $this->appointment_date->format('d M Y') . ' at ' . $this->getFormattedTime();
    }

    public function getFormattedDate(): string
    {
        return $this->appointment_date->format('d M Y');
    }

    public function getFormattedTime(): string
    {
        $time = $this->appointment_time instanceof Carbon
            ? $this->appointment_time
            : Carbon::parse($this->appointment_time);
        return $time->format('h:i A');
    }

    /**
     * Check if the appointment is upcoming (in the future).
     */
    public function isUpcoming(): bool
    {
        $dt = $this->getAppointmentDateTime();
        return $dt->isFuture();
    }

    /**
     * Get human-readable time until the appointment.
     */
    public function getTimeUntil(): string
    {
        $dt = $this->getAppointmentDateTime();

        if ($dt->isPast()) {
            return 'Appointment has passed';
        }

        return $dt->diffForHumans();
    }

    private function getAppointmentDateTime(): Carbon
    {
        $dateStr = $this->appointment_date instanceof Carbon
            ? $this->appointment_date->format('Y-m-d')
            : Carbon::parse($this->appointment_date)->format('Y-m-d');

        $time = $this->appointment_time instanceof Carbon
            ? $this->appointment_time
            : Carbon::parse($this->appointment_time);
        $timeStr = $time->format('H:i:s');

        return Carbon::createFromFormat('Y-m-d H:i:s', $dateStr . ' ' . $timeStr);
    }

    // Check if appointment can be cancelled
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               $this->appointment_date->isAfter(now()->addHours(24));
    }

    // Check if appointment can be confirmed
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    // Check if appointment can be rejected
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    // Get available time slots for a given date
    public static function getAvailableTimeSlots(Carbon $date): array
    {
        $morningSlots = [
            '08:30:00' => '8:30 AM',
            '09:30:00' => '9:30 AM',
            '10:30:00' => '10:30 AM',
            '11:30:00' => '11:30 AM',
        ];

        $afternoonSlots = [
            '12:30:00' => '12:30 PM',
            '13:30:00' => '1:30 PM',
            '14:30:00' => '2:30 PM',
            '15:30:00' => '3:30 PM',
        ];

        $allSlots = array_merge($morningSlots, $afternoonSlots);

        // Get booked slots for the date
        $bookedSlots = self::where('appointment_date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i:s');
            })
            ->toArray();

        // Filter out booked slots
        $availableSlots = array_filter($allSlots, function ($time) use ($bookedSlots) {
            return !in_array($time, $bookedSlots);
        }, ARRAY_FILTER_USE_KEY);

        return $availableSlots;
    }

    // Get all time slots (for display purposes)
    public static function getAllTimeSlots(): array
    {
        return [
            '08:30:00' => '8:30 AM',
            '09:30:00' => '9:30 AM',
            '10:30:00' => '10:30 AM',
            '11:30:00' => '11:30 AM',
            '12:30:00' => '12:30 PM',
            '13:30:00' => '1:30 PM',
            '14:30:00' => '2:30 PM',
            '15:30:00' => '3:30 PM',
        ];
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
