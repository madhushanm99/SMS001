<x-customer.layouts.app :title="'Appointment Details'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Appointment Details - {{ $appointment->appointment_no }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                @if($appointment->canBeCancelled())
                    <form method="POST" action="{{ route('customer.appointments.cancel', $appointment) }}"
                          class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Cancel Appointment
                        </button>
                    </form>
                @endif
            </div>
            <a href="{{ route('customer.appointments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Appointments
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Appointment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Appointment Information</h6>
                    <div>
                        <span class="badge bg-{{ $appointment->getServiceTypeColor() }} fs-6">
                            {{ $appointment->getServiceTypeLabel() }}
                        </span>
                        <span class="badge bg-{{ $appointment->getStatusColor() }} fs-6 ms-2">
                            {{ $appointment->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Appointment Number</label>
                            <div class="h5 text-dark">{{ $appointment->appointment_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Appointment Date & Time</label>
                            <div class="h5 text-dark">{{ $appointment->getFormattedDateTime() }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Vehicle</label>
                            <div class="h5 text-dark">
                                {{ $appointment->vehicle_no }}
                                @if($appointment->vehicle)
                                    <br><small class="text-muted">
                                        {{ $appointment->vehicle->brand->name ?? '' }} {{ $appointment->vehicle->model }}
                                        ({{ $appointment->vehicle->year_of_manufacture }})
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Service Type</label>
                            <div class="h5 text-dark">
                                <span class="badge bg-{{ $appointment->getServiceTypeColor() }} fs-6">
                                    {{ $appointment->getServiceTypeLabel() }}
                                </span>
                            </div>
                            <small class="text-muted">
                                @if($appointment->service_type === 'NS')
                                    Basic maintenance, oil change, filters, and routine inspection
                                @elseif($appointment->service_type === 'FS')
                                    Comprehensive inspection, maintenance, and detailed service
                                @endif
                            </small>
                        </div>
                    </div>

                    @if($appointment->customer_notes)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted">Your Notes</label>
                            <div class="p-3 bg-light rounded">
                                {{ $appointment->customer_notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($appointment->staff_notes)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted">Staff Notes</label>
                            <div class="p-3 bg-info bg-opacity-10 rounded border-start border-info border-4">
                                {{ $appointment->staff_notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($appointment->handled_by || $appointment->handled_at)
                    <hr>
                    <div class="row">
                        @if($appointment->handled_by)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Handled By</label>
                            <div class="text-dark">{{ $appointment->handled_by }}</div>
                        </div>
                        @endif
                        @if($appointment->handled_at)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Handled On</label>
                            <div class="text-dark">{{ $appointment->handled_at->format('d M Y, h:i A') }}</div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Appointment Status and Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status & Actions</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="mb-2">
                            <span class="badge bg-{{ $appointment->getStatusColor() }} fs-6">
                                {{ $appointment->getStatusLabel() }}
                            </span>
                        </div>

                        @switch($appointment->status)
                            @case('pending')
                                <small class="text-warning">
                                    <i class="bi bi-clock"></i>
                                    Your appointment is waiting for approval from our staff.
                                </small>
                                @break
                            @case('confirmed')
                                <small class="text-success">
                                    <i class="bi bi-check-circle"></i>
                                    Your appointment has been confirmed. Please arrive on time.
                                </small>
                                @break
                            @case('rejected')
                                <small class="text-danger">
                                    <i class="bi bi-x-circle"></i>
                                    Your appointment was rejected. Please check staff notes for details.
                                </small>
                                @break
                            @case('completed')
                                <small class="text-info">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Your service has been completed successfully.
                                </small>
                                @break
                            @case('cancelled')
                                <small class="text-secondary">
                                    <i class="bi bi-x-circle"></i>
                                    This appointment has been cancelled.
                                </small>
                                @break
                        @endswitch
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        @if($appointment->canBeCancelled())
                            <form method="POST" action="{{ route('customer.appointments.cancel', $appointment) }}"
                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Cancel Appointment
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'rejected' || $appointment->status === 'cancelled')
                            <a href="{{ route('customer.appointments.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Book New Appointment
                            </a>
                        @endif

                        <button class="btn btn-info" disabled>
                            <i class="bi bi-telephone"></i> Contact Support
                            <small class="d-block">Coming Soon</small>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Appointment Summary -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="bi bi-calendar-event display-4 text-primary"></i>
                        <h5 class="mt-2">{{ $appointment->getFormattedDate() }}</h5>
                        <p class="text-muted mb-2">{{ $appointment->getFormattedTime() }}</p>
                        <div class="mb-3">
                            <span class="badge bg-{{ $appointment->getServiceTypeColor() }}">
                                {{ $appointment->getServiceTypeLabel() }}
                            </span>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Booked on {{ $appointment->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer.layouts.app>
