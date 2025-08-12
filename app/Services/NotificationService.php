<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentNotification;
use App\Events\AppointmentStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Send notification when appointment is created
     */
    public static function appointmentCreated(Appointment $appointment): void
    {
        $title = 'New Appointment Request';
        $message = "New appointment request from {$appointment->customer->name} for {$appointment->vehicle_no} on {$appointment->getFormattedDateTime()}";

        // Get all staff users (assuming they have a specific role or all users are staff)
        $staffUsers = User::all(); // Adjust this query based on your user roles system

        // Send notification to all staff
        Notification::send($staffUsers, new AppointmentNotification(
            $appointment,
            'appointment_created',
            $title,
            $message
        ));

        // Broadcast the event
        event(new AppointmentStatusChanged($appointment, null, 'pending', $appointment->customer->name));
    }

    /**
     * Send notification when appointment is confirmed
     */
    public static function appointmentConfirmed(Appointment $appointment, string $handledBy): void
    {
        $title = 'Appointment Confirmed';
        $message = "Appointment {$appointment->appointment_no} has been confirmed by {$handledBy}";

        // Get all staff users
        $staffUsers = User::all();

        // Send notification to all staff
        Notification::send($staffUsers, new AppointmentNotification(
            $appointment,
            'appointment_confirmed',
            $title,
            $message
        ));

        // Broadcast the event
        event(new AppointmentStatusChanged($appointment, 'pending', 'confirmed', $handledBy));
    }

    /**
     * Send notification when appointment is rejected
     */
    public static function appointmentRejected(Appointment $appointment, string $handledBy, ?string $reason = null): void
    {
        $title = 'Appointment Rejected';
        $message = "Appointment {$appointment->appointment_no} has been rejected by {$handledBy}";
        if ($reason) {
            $message .= ". Reason: {$reason}";
        }

        // Get all staff users
        $staffUsers = User::all();

        // Send notification to all staff
        Notification::send($staffUsers, new AppointmentNotification(
            $appointment,
            'appointment_rejected',
            $title,
            $message
        ));

        // Broadcast the event
        event(new AppointmentStatusChanged($appointment, 'pending', 'rejected', $handledBy));
    }

    /**
     * Send notification when appointment is cancelled
     */
    public static function appointmentCancelled(Appointment $appointment): void
    {
        $title = 'Appointment Cancelled';
        $message = "Appointment {$appointment->appointment_no} has been cancelled by {$appointment->customer->name}";

        // Get all staff users
        $staffUsers = User::all();

        // Send notification to all staff
        Notification::send($staffUsers, new AppointmentNotification(
            $appointment,
            'appointment_cancelled',
            $title,
            $message
        ));

        // Broadcast the event
        event(new AppointmentStatusChanged($appointment, 'confirmed', 'cancelled', $appointment->customer->name));
    }

    /**
     * Send notification when appointment is completed
     */
    public static function appointmentCompleted(Appointment $appointment, string $handledBy): void
    {
        $title = 'Appointment Completed';
        $message = "Appointment {$appointment->appointment_no} has been completed by {$handledBy}";

        // Get all staff users
        $staffUsers = User::all();

        // Send notification to all staff
        Notification::send($staffUsers, new AppointmentNotification(
            $appointment,
            'appointment_completed',
            $title,
            $message
        ));

        // Broadcast the event
        event(new AppointmentStatusChanged($appointment, 'confirmed', 'completed', $handledBy));
    }

    /**
     * Get unread notification count for current user
     */
    public static function getUnreadCount(?User $user = null): int
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    /**
     * Get recent notifications for current user
     */
    public static function getRecentNotifications(?User $user = null, int $limit = 10)
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return collect();
        }

        // Only keep New Appointment Request and Appointment Cancelled
        return $user->notifications()
            ->whereIn('data->type', ['appointment_created', 'appointment_cancelled'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead(string $notificationId, ?User $user = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        $notification = $user->notifications()->find($notificationId);

        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(?User $user = null): int
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return 0;
        }

        return $user->unreadNotifications()->update(['read_at' => now()]);
    }
}
