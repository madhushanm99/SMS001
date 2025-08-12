<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Get unread notification count for AJAX requests
     */
    public function getUnreadCount()
    {
        $count = NotificationService::getUnreadCount();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $notifications = NotificationService::getRecentNotifications(null, $limit);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => NotificationService::getUnreadCount()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $success = NotificationService::markAsRead($notificationId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification marked as read' : 'Notification not found'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $count = NotificationService::markAllAsRead();

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read"
        ]);
    }

    /**
     * Get all notifications with pagination
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $query = $user->notifications();

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', 'like', "%{$request->type}%");
        }

        $notifications = $query->latest()->paginate(10)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show specific notification and mark as read
     */
    public function show($notificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            abort(404, 'Notification not found');
        }

        // Mark as read when viewed
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        // Redirect to the appointment if it's an appointment notification
        if (isset($notification->data['appointment_id'])) {
            return redirect()->route('appointments.show', $notification->data['appointment_id']);
        }

        return view('notifications.show', compact('notification'));
    }
}
