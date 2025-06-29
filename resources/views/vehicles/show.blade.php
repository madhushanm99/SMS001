<x-layout> <x-slot name="title">Vehicle Profile</x-slot>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item"> <a class="nav-link active" href="#details" data-toggle="tab">Details</a> </li>
                <li class="nav-item"> <a class="nav-link" href="#history" data-toggle="tab">Service History</a> </li>
            </ul>
        </div>
        <div class="tab-content card-body">
            <div class="tab-pane active" id="details">
                <p><strong>Customer:</strong> {{ $vehicle->customer->name }}</p>
                <p><strong>Reg No:</strong> {{ $vehicle->vehicle_no }}</p>
                <p><strong>Brand:</strong> {{ $vehicle->brand->name ?? '-' }}</p>
                <p><strong>Model:</strong> {{ $vehicle->model }}</p>
                <p><strong>Engine:</strong> {{ $vehicle->engine_no }}</p>
                <p><strong>Chassis:</strong> {{ $vehicle->chassis_no }}</p>
                <p><strong>Route:</strong> {{ $vehicle->route->name ?? '-' }}</p>
                <p><strong>Year:</strong> {{ $vehicle->year_of_manufacture }}</p>
                <p><strong>Purchased:</strong> {{ $vehicle->date_of_purchase?->format('Y-m-d') }}</p>
                <p><strong>Last Entry:</strong> {{ $vehicle->last_entry?->format('Y-m-d H:i') ?? '-' }}</p>
                <p> <strong>Registration:</strong> {!! $vehicle->registration_status
                    ? '<span class="badge badge-success">Complete</span>'
                    : '<span class="badge badge-warning">Pending</span>' !!} </p>
            </div>
            <div class="tab-pane" id="history">
                <p class="text-muted">Service history will be shown here later.</p>
            </div>
        </div>
    </div>
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary mt-3">← Back</a>
</x-layout>
