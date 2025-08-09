<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'user_id',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Create a new notification
    public static function createNotification(
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $userId = null,
        ?string $createdBy = null
    ): self {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => $userId,
            'created_by' => $createdBy,
            'is_read' => false,
        ]);
    }

    // Mark notification as read
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    // Mark notification as unread
    public function markAsUnread(): void
    {
        $this->update(['is_read' => false]);
    }

    // Get notification icon based on type
    public function getIconClass(): string
    {
        return match($this->type) {
            'appointment_created' => 'bi-calendar-plus',
            'appointment_confirmed' => 'bi-check-circle',
            'appointment_rejected' => 'bi-x-circle',
            'appointment_cancelled' => 'bi-calendar-x',
            'service_completed' => 'bi-wrench',
            'payment_received' => 'bi-currency-dollar',
            default => 'bi-bell'
        };
    }

    // Get notification color based on type
    public function getColorClass(): string
    {
        return match($this->type) {
            'appointment_created' => 'text-primary',
            'appointment_confirmed' => 'text-success',
            'appointment_rejected' => 'text-danger',
            'appointment_cancelled' => 'text-warning',
            'service_completed' => 'text-info',
            'payment_received' => 'text-success',
            default => 'text-secondary'
        };
    }

    // Get formatted time ago
    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static methods for common notification operations
    public static function getUnreadCount(?string $userId = null): int
    {
        $query = self::unread();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        } else {
            $query->whereNull('user_id');
        }

        return $query->count();
    }

    public static function getRecentNotifications(?string $userId = null, int $limit = 10)
    {
        $query = self::query();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        } else {
            $query->whereNull('user_id');
        }

        return $query->latest()->limit($limit)->get();
    }

    public static function markAllAsRead(?string $userId = null): void
    {
        $query = self::unread();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        } else {
            $query->whereNull('user_id');
        }

        $query->update(['is_read' => true]);
    }
}
