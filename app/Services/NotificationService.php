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

    // Removed appointmentConfirmed notifications per requirements.

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

    // Removed appointmentCompleted notifications per requirements.

    /**
     * Get unread notification count for current user
     */
    public static function getUnreadCount(?User $user = null): int
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return 0;
        }

        // Only count permitted notification types
        return $user->unreadNotifications()
            ->whereIn('data->type', [
                'appointment_created',
                'appointment_completed',
                'low_stock',
            ])
            ->count();
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

        // Only keep New Appointment Request, Appointment Completed, and Low Stock
        return $user->notifications()
            ->whereIn('data->type', ['appointment_created', 'appointment_completed', 'low_stock'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Send a low stock notification to all staff when reorder level is hit.
     */
    public static function lowStockReached(string $itemId, string $itemName, int $currentQty, int $reorderLevel): void
    {
        try {
            $staffUsers = User::all();
            $title = 'Low Stock Alert';
            $message = "{$itemName} ({$itemId}) qty {$currentQty}  reorder level {$reorderLevel}";
            Notification::send($staffUsers, new \App\Notifications\LowStockNotification(
                $itemId,
                $itemName,
                $currentQty,
                $reorderLevel,
                $title,
                $message
            ));
        } catch (\Throwable $e) {
            // Silent fail for notification errors
        }
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
