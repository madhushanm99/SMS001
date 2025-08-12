<x-layout>
    <x-slot name="title">Notifications</x-slot>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                <i class="bi bi-check-all"></i> Mark All as Read
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('notifications.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" name="status">
                        <option value="">All Notifications</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read Only</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="appointment_created" {{ request('type') == 'appointment_created' ? 'selected' : '' }}>New Appointment Request</option>
                        <option value="appointment_cancelled" {{ request('type') == 'appointment_cancelled' ? 'selected' : '' }}>Appointment Cancelled</option>
                        <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $type = $data['type'] ?? null;
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $isUnread = is_null($notification->read_at);
                    $icon = match($type) {
                        'appointment_created' => 'bi bi-info-circle',
                        'appointment_cancelled' => 'bi bi-dash-circle',
                        'low_stock' => 'bi bi-exclamation-triangle',
                        default => 'bi bi-info-circle',
                    };
                    $link = $data['url'] ?? (isset($data['appointment_id']) ? route('appointments.show', $data['appointment_id']) : '#');
                @endphp
                <div class="d-flex align-items-center p-3 border-bottom {{ $isUnread ? 'bg-light' : '' }}">
                    <div class="mr-3">
                        <div class="icon-circle {{ $isUnread ? 'bg-primary' : 'bg-secondary' }}">
                            <i class="{{ $icon }} text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 {{ $isUnread ? 'font-weight-bold' : '' }}">{{ $title }}</h6>
                                <p class="mb-1 text-muted">{{ $message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="ml-3">
                                @if($isUnread)
                                    <span class="badge badge-primary">New</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                                @if($link && $link !== '#')
                                    <a href="{{ $link }}" class="btn btn-sm btn-outline-primary">Open</a>
                                @endif
                        </div>
                    </div>
                    <div class="ml-2">
                        @if($isUnread)
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="markAsRead('{{ $notification->id }}')">
                                Mark as Read
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} notifications
                        </small>
                    </div>
                    <div>
                        {{ $notifications->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash display-1 text-muted"></i>
                    <h4 class="mt-3">No Notifications Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'type']))
                            No notifications match your current filters.
                        @else
                            You don't have any notifications yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}
</script>
@endpush

@push('styles')
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

</x-layout>
