<x-layout> <x-slot name="title">Appointments</x-slot>
<div class="pagetitle">
    <h1>Appointment Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">{{ $appointment->appointment_no }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Appointment Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Appointment Information</h5>
                        <div>
                            <span class="badge fs-6" style="background-color: {{ $appointment->getStatusColor() }};">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Appointment No:</td>
                                    <td>{{ $appointment->appointment_no }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date & Time:</td>
                                    <td>
                                        <strong>{{ $appointment->getFormattedDate() }}</strong><br>
                                        <span class="text-muted">{{ $appointment->getFormattedTime() }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Service Type:</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $appointment->getServiceTypeColor() }};">
                                            {{ $appointment->getServiceTypeLabel() }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $appointment->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($appointment->handled_by)
                                <tr>
                                    <td class="fw-bold">Handled By:</td>
                                    <td>{{ $appointment->handled_by }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Handled At:</td>
                                    <td>{{ $appointment->handled_at ? $appointment->handled_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                </tr>
                                @endif
                                @if($appointment->isUpcoming())
                                <tr>
                                    <td class="fw-bold">Time Until:</td>
                                    <td class="text-primary">{{ $appointment->getTimeUntil() }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($appointment->customer_notes)
                    <div class="mt-4">
                        <h6 class="fw-bold">Customer Notes:</h6>
                        <div class="alert alert-light">
                            {{ $appointment->customer_notes }}
                        </div>
                    </div>
                    @endif

                    @if($appointment->staff_notes)
                    <div class="mt-4">
                        <h6 class="fw-bold">Staff Notes:</h6>
                        <div class="alert alert-info">
                            {{ $appointment->staff_notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Customer Information</h5>

                    @if($appointment->customer)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $appointment->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>
                                        <a href="mailto:{{ $appointment->customer->email }}">
                                            {{ $appointment->customer->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phone:</td>
                                    <td>
                                        @if($appointment->customer->phone)
                                            <a href="tel:{{ $appointment->customer->phone }}">
                                                {{ $appointment->customer->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Customer ID:</td>
                                    <td>{{ $appointment->customer->custom_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Address:</td>
                                    <td>
                                        @if($appointment->customer->address)
                                            {{ $appointment->customer->address }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Registered:</td>
                                    <td>{{ $appointment->customer->created_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Customer information not available.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Vehicle Information</h5>

                    @if($appointment->vehicle)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Vehicle Number:</td>
                                    <td>{{ $appointment->vehicle_no }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Brand:</td>
                                    <td>{{ $appointment->vehicle->brand->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Model:</td>
                                    <td>{{ $appointment->vehicle->model }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Year:</td>
                                    <td>{{ $appointment->vehicle->year_of_manufacture }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Engine Number:</td>
                                    <td>{{ $appointment->vehicle->engine_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Chassis Number:</td>
                                    <td>{{ $appointment->vehicle->chassis_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Color:</td>
                                    <td>{{ $appointment->vehicle->color ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Purchase Date:</td>
                                    <td>
                                        @if($appointment->vehicle->date_of_purchase)
                                            {{ \Carbon\Carbon::parse($appointment->vehicle->date_of_purchase)->format('d M Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Vehicle information not available for {{ $appointment->vehicle_no }}.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>

                    <div class="d-grid gap-2">
                        @if($appointment->status === 'pending')
                        <button type="button" class="btn btn-success" onclick="confirmAppointment()">
                            <i class="bi bi-check-circle"></i> Confirm Appointment
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectAppointment()">
                            <i class="bi bi-x-circle"></i> Reject Appointment
                        </button>
                        @endif

                        @if($appointment->status === 'confirmed')
                        <button type="button" class="btn btn-info" onclick="completeAppointment()">
                            <i class="bi bi-check-square"></i> Mark as Complete
                        </button>
                        @endif

                        @if($appointment->customer)
                        <a href="mailto:{{ $appointment->customer->email }}" class="btn btn-outline-primary">
                            <i class="bi bi-envelope"></i> Send Email
                        </a>

                        @if($appointment->customer->phone)
                        <a href="tel:{{ $appointment->customer->phone }}" class="btn btn-outline-success">
                            <i class="bi bi-telephone"></i> Call Customer
                        </a>
                        @endif
                        @endif

                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Service History (if available) -->
            @if($appointment->customer && $appointment->customer->serviceInvoices->count() > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Service History</h5>

                    @foreach($appointment->customer->serviceInvoices->take(5) as $service)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <small class="text-muted">{{ $service->invoice_no }}</small><br>
                            <span class="badge" style="background-color: {{ $service->getServiceTypeColor() }};">
                                {{ $service->getServiceTypeLabel() }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $service->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Action Modal -->
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
                        <label for="staffNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="staffNotes" rows="4"
                                  placeholder="Add any notes about this action..."></textarea>
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

function confirmAppointment() {
    currentAction = 'confirm';
    document.getElementById('actionModalTitle').textContent = 'Confirm Appointment';
    document.getElementById('actionConfirmBtn').textContent = 'Confirm Appointment';
    document.getElementById('actionConfirmBtn').className = 'btn btn-success';
    document.getElementById('staffNotes').placeholder = 'Add any confirmation notes...';
    document.getElementById('staffNotes').value = '';
    new bootstrap.Modal(document.getElementById('actionModal')).show();
}

function rejectAppointment() {
    currentAction = 'reject';
    document.getElementById('actionModalTitle').textContent = 'Reject Appointment';
    document.getElementById('actionConfirmBtn').textContent = 'Reject Appointment';
    document.getElementById('actionConfirmBtn').className = 'btn btn-danger';
    document.getElementById('staffNotes').placeholder = 'Please provide reason for rejection...';
    document.getElementById('staffNotes').value = '';
    new bootstrap.Modal(document.getElementById('actionModal')).show();
}

function completeAppointment() {
    currentAction = 'complete';
    document.getElementById('actionModalTitle').textContent = 'Complete Appointment';
    document.getElementById('actionConfirmBtn').textContent = 'Mark Complete';
    document.getElementById('actionConfirmBtn').className = 'btn btn-info';
    document.getElementById('staffNotes').placeholder = 'Add completion notes...';
    document.getElementById('staffNotes').value = '';
    new bootstrap.Modal(document.getElementById('actionModal')).show();
}

document.getElementById('actionConfirmBtn').addEventListener('click', function() {
    const staffNotes = document.getElementById('staffNotes').value;

    if (currentAction === 'reject' && !staffNotes.trim()) {
        alert('Please provide a reason for rejection.');
        return;
    }

    const url = `/appointments/{{ $appointment->id }}/${currentAction}`;

    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
