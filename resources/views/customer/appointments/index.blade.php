<x-customer.layouts.app :title="'My Appointments'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Appointments</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('customer.appointments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Book New Appointment
                </a>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index') }}">All Appointments</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Status</h6></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['status' => 'pending']) }}">Pending</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['status' => 'confirmed']) }}">Confirmed</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['status' => 'completed']) }}">Completed</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['status' => 'rejected']) }}">Rejected</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Service Type</h6></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['service_type' => 'NS']) }}">Normal Service</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.appointments.index', ['service_type' => 'FS']) }}">Full Service</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confirmed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $confirmedAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Next Appointment</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($nextAppointment)
                                    {{ $nextAppointment->getFormattedDate() }}
                                    <br><small>{{ $nextAppointment->getFormattedTime() }}</small>
                                @else
                                    No upcoming appointments
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-event fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('customer.appointments.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="Search by appointment number, vehicle, or notes..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment History</h6>
        </div>
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Appointment #</th>
                                <th>Date & Time</th>
                                <th>Vehicle</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                            <tr>
                                <td>
                                    <strong>{{ $appointment->appointment_no }}</strong>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $appointment->getFormattedDate() }}</div>
                                    <small class="text-muted">{{ $appointment->getFormattedTime() }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $appointment->vehicle_no }}</div>
                                    @if($appointment->vehicle)
                                        <small class="text-muted">
                                            {{ $appointment->vehicle->brand->name ?? '' }} {{ $appointment->vehicle->model }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->getServiceTypeColor() }}">
                                        {{ $appointment->getServiceTypeLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->getStatusColor() }}">
                                        {{ $appointment->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        @if($appointment->customer_notes)
                                            <small><strong>Your notes:</strong> {{ Str::limit($appointment->customer_notes, 50) }}</small>
                                        @endif
                                        @if($appointment->staff_notes)
                                            <br><small class="text-info"><strong>Staff notes:</strong> {{ Str::limit($appointment->staff_notes, 50) }}</small>
                                        @endif
                                        @if(!$appointment->customer_notes && !$appointment->staff_notes)
                                            <small class="text-muted">No notes</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.appointments.show', $appointment) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($appointment->canBeCancelled())
                                            <form method="POST" action="{{ route('customer.appointments.cancel', $appointment) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
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
                            Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }} of {{ $appointments->total() }} appointments
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
                        @if(request()->hasAny(['search', 'status', 'service_type']))
                            No appointments match your current filters.
                            <a href="{{ route('customer.appointments.index') }}" class="btn btn-link">Clear filters</a>
                        @else
                            You haven't booked any appointments yet.
                        @endif
                    </p>
                    <a href="{{ route('customer.appointments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Book Your First Appointment
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .text-gray-300 {
            color: #dddfeb !important;
        }
    </style>
    @endpush
</x-customer.layouts.app>
