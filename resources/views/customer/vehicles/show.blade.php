<x-customer.layouts.app :title="'Vehicle Details'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Vehicle Details - {{ $vehicle->vehicle_no }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Vehicle
                </a>
            </div>
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Vehicles
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vehicle Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Vehicle Number</label>
                            <div class="h5 text-dark">{{ $vehicle->vehicle_no }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Brand</label>
                            <div class="h5 text-dark">{{ $vehicle->brand->name ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Model</label>
                            <div class="h5 text-dark">{{ $vehicle->model }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Year of Manufacture</label>
                            <div class="h5 text-dark">{{ $vehicle->year_of_manufacture }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Engine Number</label>
                            <div class="h5 text-dark">{{ $vehicle->engine_no }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Chassis Number</label>
                            <div class="h5 text-dark">{{ $vehicle->chassis_no }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Route</label>
                            <div class="h5 text-dark">{{ $vehicle->route->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Date of Purchase</label>
                            <div class="h5 text-dark">{{ $vehicle->date_of_purchase->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Registration Status</label>
                            <div>
                                @if($vehicle->registration_status)
                                    <span class="badge bg-success fs-6">Registered with DMT</span>
                                @else
                                    <span class="badge bg-warning fs-6">Registration Pending</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                @if($vehicle->status)
                                    <span class="badge bg-success fs-6">Active</span>
                                @else
                                    <span class="badge bg-danger fs-6">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Next Service (approximate)</label>
                            @php($s = $vehicle->serviceSchedule)
                            <div>
                                @if($s && ($s->next_service_date || $s->next_service_mileage))
                                    {{ $s->next_service_date?->format('d M Y') ?? '—' }} /
                                    {{ $s->next_service_mileage ? number_format($s->next_service_mileage) . ' km' : '—' }}
                                @else
                                    <span class="text-muted">no data to show</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($vehicle->last_entry)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Last Service Entry</label>
                            <div class="h5 text-dark">{{ $vehicle->last_entry->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Vehicle Details
                        </a>
                        <button class="btn btn-info" disabled>
                            <i class="bi bi-calendar-check"></i> Book Service
                            <small class="d-block">Coming Soon</small>
                        </button>
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-file-text"></i> View Service History
                            <small class="d-block">Coming Soon</small>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vehicle Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="bi bi-car-front display-1 text-primary"></i>
                        <h4 class="mt-2">{{ $vehicle->brand->name ?? 'N/A' }} {{ $vehicle->model }}</h4>
                        <p class="text-muted">{{ $vehicle->vehicle_no }}</p>
                        <div class="mt-3">
                            <small class="text-muted">
                                Registered on {{ $vehicle->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer.layouts.app>
