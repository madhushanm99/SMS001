<x-layout>
    <x-slot name="title">Appointments</x-slot>
    <div class="pagetitle">
        <h1>Appointment Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Appointments</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Pending <span>| Today</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $pendingAppointments ?? 0 }}</h6>
                                <span class="text-warning small pt-1 fw-bold">Pending Approval</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Confirmed <span>| Today</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $confirmedAppointments ?? 0 }}</h6>
                                <span class="text-success small pt-1 fw-bold">Confirmed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Total <span>| Appointments</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-day"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $todayAppointments ?? 0 }}</h6>
                                <span class="text-primary small pt-1 fw-bold">Total Today</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Total <span>| All Time</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $totalAppointments ?? 0 }}</h6>
                                <span class="text-info small pt-1 fw-bold">Total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filter Appointments</h5>

                        <form method="GET" action="{{ route('appointments.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                                        Confirmed</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Rejected</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="service_type" class="form-label">Service Type</label>
                                <select class="form-select" name="service_type" id="service_type">
                                    <option value="">All Types</option>
                                    <option value="NS" {{ request('service_type') == 'NS' ? 'selected' : '' }}>
                                        Normal
                                        Service</option>
                                    <option value="FS" {{ request('service_type') == 'FS' ? 'selected' : '' }}>Full
                                        Service</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date"
                                    value="{{ request('date') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" id="search"
                                    placeholder="Customer name, vehicle..." value="{{ request('search') }}">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Appointments List</h5>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-calendar-week"></i> Calendar View
                            </a>
                        </div>

                        @if ($appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Appointment #</th>
                                            <th>Customer</th>
                                            <th>Vehicle</th>
                                            <th>Service Type</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($appointments as $appointment)
                                            <tr>
                                                <td>
                                                    <strong>{{ $appointment->appointment_no }}</strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $appointment->customer->name ?? 'N/A' }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $appointment->customer->email ?? '' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $appointment->vehicle_no }}</strong><br>
                                                        @if ($appointment->vehicle)
                                                            <small class="text-muted">
                                                                {{ $appointment->vehicle->brand->name ?? '' }}
                                                                {{ $appointment->vehicle->model }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: {{ $appointment->getServiceTypeColor() }};">
                                                        {{ $appointment->getServiceTypeLabel() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $appointment->getFormattedDate() }}</strong><br>
                                                        <small>{{ $appointment->getFormattedTime() }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: {{ $appointment->getStatusColor() }};">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                    @if ($appointment->status === 'pending')
                                                        <br><small class="text-warning">
                                                            <i class="bi bi-exclamation-triangle"></i> Needs Action
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('appointments.show', $appointment) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        @if ($appointment->status === 'pending')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-success"
                                                                onclick="confirmAppointment({{ $appointment->id }})"
                                                                title="Confirm">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="rejectAppointment({{ $appointment->id }})"
                                                                title="Reject">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        @endif

                                                        @if ($appointment->status === 'confirmed')
                                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                                onclick="completeAppointment({{ $appointment->id }})"
                                                                title="Mark Complete">
                                                                <i class="bi bi-check-square"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }}
                                        of {{ $appointments->total() }} appointments
                                    </small>
                                </div>
                                <div>
                                    {{ $appointments->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                                <h4 class="mt-3">No Appointments Found</h4>
                                <p class="text-muted">
                                    @if (request()->hasAny(['status', 'service_type', 'date', 'search']))
                                        No appointments match your current filters.
                                    @else
                                        No appointments have been scheduled yet.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="actionForm">
                        <div class="mb-3">
                            <label for="staffNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="staffNotes" rows="3" placeholder="Add any notes about this action..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="actionConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentAction = '';
            let currentAppointmentId = '';

            function confirmAppointment(appointmentId) {
                currentAction = 'confirm';
                currentAppointmentId = appointmentId;
                document.getElementById('actionModalTitle').textContent = 'Confirm Appointment';
                document.getElementById('actionConfirmBtn').textContent = 'Confirm Appointment';
                document.getElementById('actionConfirmBtn').className = 'btn btn-success';
                document.getElementById('staffNotes').placeholder = 'Add any confirmation notes...';
                new bootstrap.Modal(document.getElementById('actionModal')).show();
            }

            function rejectAppointment(appointmentId) {
                currentAction = 'reject';
                currentAppointmentId = appointmentId;
                document.getElementById('actionModalTitle').textContent = 'Reject Appointment';
                document.getElementById('actionConfirmBtn').textContent = 'Reject Appointment';
                document.getElementById('actionConfirmBtn').className = 'btn btn-danger';
                document.getElementById('staffNotes').placeholder = 'Please provide reason for rejection...';
                new bootstrap.Modal(document.getElementById('actionModal')).show();
            }

            function completeAppointment(appointmentId) {
                currentAction = 'complete';
                currentAppointmentId = appointmentId;
                document.getElementById('actionModalTitle').textContent = 'Complete Appointment';
                document.getElementById('actionConfirmBtn').textContent = 'Mark Complete';
                document.getElementById('actionConfirmBtn').className = 'btn btn-info';
                document.getElementById('staffNotes').placeholder = 'Add completion notes...';
                new bootstrap.Modal(document.getElementById('actionModal')).show();
            }

            document.getElementById('actionConfirmBtn').addEventListener('click', function() {
                const staffNotes = document.getElementById('staffNotes').value;

                if (currentAction === 'reject' && !staffNotes.trim()) {
                    alert('Please provide a reason for rejection.');
                    return;
                }

                if (!currentAppointmentId || !currentAction) {
                    console.error('Missing appointment id or action');
                    alert('An error occurred. Please try again.');
                    return;
                }

                const url = `/appointments/${currentAppointmentId}/${currentAction}`;

                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            staff_notes: staffNotes
                        })
                    })
                    .then(async response => {
                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error(text || `Request failed with ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });
        </script>
    @endpush

</x-layout>
