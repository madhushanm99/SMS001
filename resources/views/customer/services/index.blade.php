<x-customer.layouts.app :title="'Service History'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Service History</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('customer.services.index') }}">All Services</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Service Type</h6></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['service_type' => 'NS']) }}">Normal Service</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['service_type' => 'FS']) }}">Full Service</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Payment Status</h6></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['payment_status' => 'unpaid']) }}">Unpaid</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['payment_status' => 'partial']) }}">Partially Paid</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['payment_status' => 'paid']) }}">Fully Paid</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Status</h6></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['status' => 'hold']) }}">On Hold</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.services.index', ['status' => 'finalized']) }}">Finalized</a></li>
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
                                Total Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalServices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wrench fa-2x text-gray-300"></i>
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
                                Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAmount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
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
                                Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPaid, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
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
                                Last Service</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $lastServiceDate ? $lastServiceDate->format('d M Y') : 'No services yet' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('customer.services.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="Search by invoice number, vehicle, or notes..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="vehicle_no">
                        <option value="">All Vehicles</option>
                        @foreach($customerVehicles as $vehicle)
                            <option value="{{ $vehicle->vehicle_no }}"
                                    {{ request('vehicle_no') == $vehicle->vehicle_no ? 'selected' : '' }}>
                                {{ $vehicle->vehicle_no }} - {{ $vehicle->brand->name ?? '' }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="service_type">
                        <option value="">All Service Types</option>
                        <option value="NS" {{ request('service_type') == 'NS' ? 'selected' : '' }}>Normal Service</option>
                        <option value="FS" {{ request('service_type') == 'FS' ? 'selected' : '' }}>Full Service</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Service History Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service History</h6>
        </div>
        <div class="card-body">
            @if($serviceInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Mileage</th>
                                <th>Service Type</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Outstanding</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceInvoices as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service->invoice_no }}</strong>
                                </td>
                                <td>{{ $service->invoice_date->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $service->vehicle_no }}</div>
                                    @if($service->vehicle)
                                        <small class="text-muted">
                                            {{ $service->vehicle->brand->name ?? '' }} {{ $service->vehicle->model }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ number_format($service->mileage) }} km</span>
                                </td>
                                <td>
                                    @if($service->service_type)
                                        <span class="badge bg-{{ $service->getServiceTypeColor() }}">
                                            {{ $service->getServiceTypeLabel() }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Not Set</span>
                                    @endif
                                </td>
                                <td>{{ number_format($service->grand_total, 2) }}</td>
                                <td class="text-success">{{ number_format($service->getTotalPayments(), 2) }}</td>
                                <td class="{{ $service->getOutstandingAmount() > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($service->getOutstandingAmount(), 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $service->status === 'finalized' ? 'success' : 'warning' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.services.show', $service) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.services.download', $service) }}"
                                           class="btn btn-sm btn-secondary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
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
                            Showing {{ $serviceInvoices->firstItem() }} to {{ $serviceInvoices->lastItem() }} of {{ $serviceInvoices->total() }} services
                        </small>
                    </div>
                    <div>
                        {{ $serviceInvoices->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-wrench display-1 text-muted"></i>
                    <h4 class="mt-3">No Service History Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'payment_status', 'service_type', 'vehicle_no']))
                            No services match your current filters.
                            <a href="{{ route('customer.services.index') }}" class="btn btn-link">Clear filters</a>
                        @else
                            You don't have any service history yet.
                        @endif
                    </p>
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
