<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700"
        rel="stylesheet">

    <!-- CSS Frameworks -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app-Dwp0M10J.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/js/app.js'])
</head>

<body>
    <x-banner />
    <div class="min-h-screen bg-gray-100">
        <x-header />
        <x-sidebar />
        <main id="main" class="main">
            {{ $slot }}
        </main>
        <x-footer />
    </div>

    <!-- JS Frameworks -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- CSRF Token Setup -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Bootstrap + Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vendor Scripts -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Alerts -->
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire("Success", "{{ session('success') }}", "success");
        </script>
    @endif

    @if (session('low_stock_alerts'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Reorder Level Reached',
                html: '<div class="text-start">' + @json(session('low_stock_alerts')).map(function(msg){return `<div class="text-danger">• ${msg}</div>`}).join('') + '</div>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        </script>
    @endif

    <!-- Real-time Notifications with Laravel Echo + Reverb -->
    <script>

    class NotificationManager {
        constructor() {
            this.notificationCount = 0;
            this.notifications = [];
            this.init();
        }

        init() {
            this.loadNotifications();
            this.setupEventListeners();
            this.setupWebSocketListeners();
            // Keep polling as fallback
            this.startPolling();
        }

        setupEventListeners() {
            // Mark all as read
            document.getElementById('markAllReadBtn')?.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });

            // Dropdown click to load notifications
            document.getElementById('notificationDropdown')?.addEventListener('click', () => {
                this.loadNotifications();
            });
        }

        setupWebSocketListeners() {
            // Wait for Echo to be available (loaded by Vite)
            const checkEcho = () => {
                if (typeof window.Echo !== 'undefined') {
                    this.initializeEcho();
                } else {
                    console.log('Waiting for Laravel Echo to load...');
                    setTimeout(checkEcho, 500);
                }
            };

            // Start checking after a short delay to allow Vite assets to load
            setTimeout(checkEcho, 1000);
        }

        initializeEcho() {
            try {
                console.log('Initializing Laravel Echo WebSocket listeners...');

                // Listen to staff notifications channel
                window.Echo.channel('staff-notifications')
                    .listen('.appointment.status.changed', (e) => {
                        console.log('Staff notification received:', e);
                        this.handleWebSocketNotification(e);
                    })
                    .error((error) => {
                        console.error('Error on staff-notifications channel:', error);
                    });

                // Listen to appointment notifications channel
                window.Echo.channel('appointments')
                    .listen('.appointment.status.changed', (e) => {
                        console.log('Appointment event received:', e);
                        this.handleWebSocketNotification(e);
                    })
                    .error((error) => {
                        console.error('Error on appointments channel:', error);
                    });

                // Listen to private user notifications (if authenticated)
                @auth
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .notification((notification) => {
                        console.log('Private notification received:', notification);
                        this.handlePrivateNotification(notification);
                    })
                    .error((error) => {
                        console.error('Error on private channel:', error);
                    });
                @endauth

                console.log('✅ WebSocket listeners initialized successfully');

                // Test connection
                this.testWebSocketConnection();
            } catch (error) {
                console.error('❌ Error setting up WebSocket listeners:', error);
            }
        }

        testWebSocketConnection() {
            // Test if WebSocket is connected
            setTimeout(() => {
                if (window.Echo.connector.pusher.connection.state === 'connected') {
                    console.log('✅ WebSocket connected successfully');
                    this.showConnectionStatus('Connected to real-time notifications', 'success');
                } else {
                    console.log('⚠️ WebSocket not connected, using polling fallback');
                    this.showConnectionStatus('Using polling for notifications', 'warning');
                }
            }, 2000);
        }

        showConnectionStatus(message, type) {
            // Optionally show a small toast notification about connection status
            console.log(`${type.toUpperCase()}: ${message}`);
        }

        handleWebSocketNotification(event) {
            // Create notification object from event
            const notification = {
                id: Date.now(), // Temporary ID
                type: 'AppointmentNotification',
                data: {
                    type: this.getNotificationTypeFromEvent(event),
                    title: this.getTitleFromEvent(event),
                    message: this.getMessageFromEvent(event),
                    appointment_id: event.appointment_id,
                    url: `/appointments/${event.appointment_id}`,
                    icon: 'bi bi-info-circle',
                    color: 'text-success'
                },
                read_at: null,
                created_at: new Date().toISOString(),
                time_ago: 'Just now'
            };

            this.addNotification(notification);
        }

        handlePrivateNotification(notification) {
            this.addNotification(notification);
        }

        getNotificationTypeFromEvent(event) {
            if (event.new_status === 'pending') return 'appointment_created';
            if (event.new_status === 'confirmed') return 'appointment_confirmed';
            if (event.new_status === 'rejected') return 'appointment_rejected';
            if (event.new_status === 'cancelled') return 'appointment_cancelled';
            if (event.new_status === 'completed') return 'appointment_completed';
            return 'appointment_updated';
        }

        getTitleFromEvent(event) {
            switch (event.new_status) {
                case 'pending': return 'New Appointment Request';
                case 'confirmed': return 'Appointment Confirmed';
                case 'rejected': return 'Appointment Rejected';
                case 'cancelled': return 'Appointment Cancelled';
                case 'completed': return 'Appointment Completed';
                default: return 'Appointment Updated';
            }
        }

        getMessageFromEvent(event) {
            const customer = event.customer_name || 'Customer';
            const vehicle = event.vehicle_no || 'Vehicle';

            switch (event.new_status) {
                case 'pending':
                    return `New appointment request from ${customer} for ${vehicle}`;
                case 'confirmed':
                    return `Appointment ${event.appointment_no} confirmed by ${event.handled_by}`;
                case 'rejected':
                    return `Appointment ${event.appointment_no} rejected by ${event.handled_by}`;
                case 'cancelled':
                    return `Appointment ${event.appointment_no} cancelled by ${customer}`;
                case 'completed':
                    return `Appointment ${event.appointment_no} completed by ${event.handled_by}`;
                default:
                    return `Appointment ${event.appointment_no} updated`;
            }
        }

        addNotification(notification) {
            this.notifications.unshift(notification);
            this.notificationCount++;
            this.updateUI();

            // Show browser notification
            this.showBrowserNotification(notification);

            // Play notification sound (optional)
            this.playNotificationSound();
        }

        // Show a native browser notification when available/allowed
        showBrowserNotification(notification) {
            try {
                if (!('Notification' in window)) {
                    return;
                }

                const title = notification?.data?.title || 'Notification';
                const body = notification?.data?.message || '';
                const url = notification?.data?.url || '#';

                if (Notification.permission === 'granted') {
                    const n = new Notification(title, {
                        body,
                        icon: '/assets/img/favicon.png'
                    });
                    n.onclick = () => {
                        if (url && url !== '#') {
                            window.open(url, '_blank');
                        }
                    };
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission().then((perm) => {
                        if (perm === 'granted') {
                            const n = new Notification(title, {
                                body,
                                icon: '/assets/img/favicon.png'
                            });
                            n.onclick = () => {
                                if (url && url !== '#') {
                                    window.open(url, '_blank');
                                }
                            };
                        }
                    }).catch(() => {
                        // ignore
                    });
                }
            } catch (e) {
                // Silently ignore notification errors
            }
        }

        playNotificationSound() {
            try {
                // Create a subtle notification sound
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(600, audioContext.currentTime + 0.1);

                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            } catch (error) {
                console.log('Could not play notification sound:', error);
            }
        }

        startPolling() {
            // Reduced polling frequency since we have WebSockets
            setInterval(() => {
                this.loadNotifications();
            }, 60000); // Every minute as fallback
        }

        async loadNotifications() {
            try {
                const response = await fetch('/notifications/recent?limit=3');
                const data = await response.json();

                this.notifications = data.notifications || [];
                this.notificationCount = data.unread_count || 0;

                this.updateUI();
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        updateUI() {
            // Update notification count
            const countBadge = document.getElementById('notificationCount');
            const headerCount = document.getElementById('headerNotificationCount');

            if (this.notificationCount > 0) {
                countBadge.textContent = this.notificationCount > 99 ? '99+' : this.notificationCount;
                countBadge.style.display = 'inline-block';
                headerCount.textContent = this.notificationCount;
            } else {
                countBadge.style.display = 'none';
                headerCount.textContent = '0';
            }

            // Update notification items
            this.renderNotifications();
        }

        renderNotifications() {
            const container = document.getElementById('notificationItems');
            if (!container) return;

            if (this.notifications.length === 0) {
                container.innerHTML = `
                    <li class="notification-item text-center">
                        <div class="p-3">
                            <i class="bi bi-bell-slash"></i>
                            <p class="mb-0">No new notifications</p>
                        </div>
                    </li>
                `;
                return;
            }

            // Ensure only the two appointment types are shown (backend already filters, but double-guard)
            const filtered = this.notifications.filter(n => {
                const t = n?.data?.type;
                return t === 'appointment_created' || t === 'appointment_completed' || t === 'low_stock';
            });
            const itemsToShow = filtered.slice(0, 3);
            container.innerHTML = itemsToShow.map(notification => {
                const url = notification.data?.url || '#';
                return `
                <li class="notification-item ${!notification.read_at ? 'unread' : ''}">
                    <a href="${url}" onclick="event.preventDefault(); notificationManager.openAndMark('${notification.id}', '${url}')">
                        <i class="${this.getNotificationIcon(notification)} ${this.getNotificationColor(notification)}"></i>
                        <div>
                            <h4>${notification.data?.title || 'Notification'}</h4>
                            <p>${this.truncateMessage(notification.data?.message || '', 60)}</p>
                            <p>${this.getTimeAgo(notification.created_at)}</p>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                `;
            }).join('');
        }

        getNotificationIcon(notification) {
            const type = notification.data?.type;
            switch(type) {
                case 'appointment_created':
                    return 'bi bi-info-circle';
                case 'appointment_completed':
                    return 'bi bi-check-square';
                case 'low_stock':
                    return 'bi bi-exclamation-triangle';
                default:
                    return 'bi bi-info-circle';
            }
        }

        getNotificationColor(notification) {
            const type = notification.data?.type;
            switch(type) {
                case 'appointment_created':
                    return 'text-success';
                case 'appointment_completed':
                    return 'text-info';
                case 'low_stock':
                    return 'text-danger';
                default:
                    return 'text-success';
            }
        }

        truncateMessage(message, length) {
            return message.length > length ? message.substring(0, length) + '...' : message;
        }

        getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours}h ago`;

            const diffInDays = Math.floor(diffInHours / 24);
            return `${diffInDays}d ago`;
        }

        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                // Reload notifications
                this.loadNotifications();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        async openAndMark(notificationId, url) {
            try {
                await this.markAsRead(notificationId);
            } finally {
                if (url && url !== '#') {
                    window.location.href = url;
                }
            }
        }

        async markAllAsRead() {
            try {
                await fetch('/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                // Reload notifications
                this.loadNotifications();
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }

        // Request browser notification permission
        requestNotificationPermission() {
            if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        }
    }

    // Initialize notification manager when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        window.notificationManager = new NotificationManager();

        // Request notification permission
        window.notificationManager.requestNotificationPermission();
    });
    </script>

    <!-- Add CSS for notification styling -->
    <style>
    .notification-item.unread {
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }

    .notification-item a {
        color: inherit;
        text-decoration: none;
        display: block;
        padding: 0.75rem;
    }

    .notification-item a:hover {
        background-color: #e9ecef;
    }

    #notificationCount {
        position: absolute;
        top: -2px;
        right: -6px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }

    /* Increase dropdown width for notifications */
    ul.dropdown-menu.notifications {
        min-width: 420px;
        max-width: 520px;
    }
    </style>

    <!-- Page-specific scripts -->
    @stack('scripts')

</body>

</html>
