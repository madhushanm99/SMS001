<!-- Notification Dropdown -->
<div class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Notifications -->
        <span class="badge badge-danger badge-counter" id="notification-count" style="display: none;">0</span>
    </a>
    <!-- Dropdown - Notifications -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
         aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Notifications Center
        </h6>

        <!-- Notification Items Container -->
        <div id="notification-items">
            <div class="text-center p-3">
                <i class="bi bi-hourglass-split"></i> Loading...
            </div>
        </div>

        <!-- Show All Notifications -->
        <a class="dropdown-item text-center small text-gray-500" href="{{ route('notifications.index') }}">
            Show All Notifications
        </a>

        <!-- Mark All as Read -->
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-center small text-gray-500" href="#" id="mark-all-read">
            <i class="bi bi-check-all"></i> Mark All as Read
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationCount = document.getElementById('notification-count');
    const notificationItems = document.getElementById('notification-items');
    const markAllReadBtn = document.getElementById('mark-all-read');

    // Load notifications on page load
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);

    function loadNotifications() {
        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    }

    function updateNotificationCount(count) {
        if (count > 0) {
            notificationCount.textContent = count > 99 ? '99+' : count;
            notificationCount.style.display = 'inline-block';
        } else {
            notificationCount.style.display = 'none';
        }
    }

    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationItems.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="bi bi-bell-slash"></i><br>
                    No new notifications
                </div>
            `;
            return;
        }

        notificationItems.innerHTML = notifications.map(notification => `
            <a class="dropdown-item d-flex align-items-center ${notification.is_read ? '' : 'bg-light'}"
               href="{{ url('/notifications') }}/${notification.id}">
                <div class="mr-3">
                    <div class="icon-circle ${notification.is_read ? 'bg-secondary' : 'bg-primary'}">
                        <i class="${notification.icon_class} text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">${notification.created_at}</div>
                    <span class="font-weight-bold">${notification.title}</span>
                    <div class="small">${notification.message.substring(0, 80)}${notification.message.length > 80 ? '...' : ''}</div>
                </div>
            </a>
        `).join('');
    }

    // Mark all as read functionality
    markAllReadBtn.addEventListener('click', function(e) {
        e.preventDefault();

        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Refresh notifications
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.badge-counter {
    position: absolute;
    transform: scale(0.7);
    transform-origin: top right;
    right: 0.25rem;
    margin-top: -0.25rem;
}

.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown-list {
    max-width: 20rem;
    max-height: 25rem;
    overflow-y: auto;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e3e6f0;
}

.dropdown-item:last-child {
    border-bottom: none;
}
</style>
@endpush
