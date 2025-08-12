<x-customer.layouts.app :title="'My Vehicles'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Vehicles</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('customer.vehicles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Register New Vehicle
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registered Vehicles</h6>
        </div>
        <div class="card-body">
            @if($vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Vehicle No</th>
                                <th>Brand & Model</th>
                                <th>Year</th>
                                <th>Route</th>
                                <th>Registration Status</th>
                                <th>Approval</th>
                                <th>Next Service</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <strong>{{ $vehicle->vehicle_no }}</strong>
                                </td>
                                <td>
                                    {{ $vehicle->brand->name ?? 'N/A' }} - {{ $vehicle->model }}
                                </td>
                                <td>{{ $vehicle->year_of_manufacture }}</td>
                                <td>{{ $vehicle->route->name ?? 'N/A' }}</td>
                                <td>
                                    @if($vehicle->registration_status)
                                        <span class="badge bg-success">Registered</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$vehicle->is_approved)
                                        <span class="badge bg-danger" title="Waiting for staff approval">Approval Pending</span>
                                    @else
                                        <span class="badge bg-primary" title="Approved for service invoices">Approved</span>
                                    @endif
                                </td>
                                <td>
                                    @php($s = $vehicle->serviceSchedule)
                                    @if($s && ($s->next_service_date || $s->next_service_mileage))
                                        <div>
                                            Date: {{ $s->next_service_date ? $s->next_service_date->format('d M Y') : '—' }}
                                        </div>
                                        <div>
                                            Mileage: {{ $s->next_service_mileage ? number_format($s->next_service_mileage) . ' km' : '—' }}
                                        </div>
                                        <small class="text-muted">Approximate</small>
                                    @else
                                        <span class="text-muted">no data to show</span>
                                    @endif
                                </td>
                                <td>{{ $vehicle->date_of_purchase->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.vehicles.show', $vehicle) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.vehicles.edit', $vehicle) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('customer.vehicles.qr', $vehicle) }}" class="btn btn-sm btn-secondary" title="Download QR">
                                            <i class="bi bi-qr-code"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-car-front display-1 text-muted"></i>
                    <h4 class="mt-3">No Vehicles Registered</h4>
                    <p class="text-muted">You haven't registered any vehicles yet.</p>
                    <a href="{{ route('customer.vehicles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Register Your First Vehicle
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-customer.layouts.app>
