<x-layout>
    <x-slot name="title">Service Schedules</x-slot>

    <div class="pagetitle">
        <h1>Service Schedules</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Service Schedules</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filter</h5>
                        <form method="GET" action="{{ route('service-schedules.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Vehicle No / Customer Name" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="due" class="form-label">Due</label>
                                <select id="due" name="due" class="form-select">
                                    <option value="">All</option>
                                    <option value="upcoming" @selected(request('due')==='upcoming')>Upcoming</option>
                                    <option value="overdue" @selected(request('due')==='overdue')>Overdue</option>
                                    <option value="none" @selected(request('due')==='none')>No Schedule</option>
                                </select>
                            </div>
                            <div class="col-md-3 align-self-end">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Apply</button>
                                <a href="{{ route('service-schedules.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Schedules</h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Vehicle No</th>
                                        <th>Customer</th>
                                        <th>Last Service</th>
                                        <th>Next Service</th>
                                        <th>Days Until Next</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $row)
                                        <tr>
                                            <td class="fw-bold">{{ $row->vehicle_no }}</td>
                                            <td>
                                                <div>{{ $row->customer_name ?? '-' }}</div>
                                                <div class="small text-muted">{{ $row->customer_email ?? '' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ optional($row->last_service_date)->format('Y-m-d') ?: '-' }}</div>
                                                <div class="small text-muted">Mileage: {{ $row->last_mileage ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ optional($row->next_service_date)->format('Y-m-d') ?: '-' }}</div>
                                                <div class="small text-muted">Mileage: {{ $row->next_service_mileage ?? '-' }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $days = $row->days_until_next;
                                                    $badgeClass = 'bg-secondary';
                                                    if (!is_null($row->next_service_date)) {
                                                        $daysDiff = \Carbon\Carbon::parse($row->next_service_date)->diffInDays(now(), false);
                                                        if ($daysDiff < 0) { $badgeClass = 'bg-primary'; }
                                                        if ($daysDiff === 0) { $badgeClass = 'bg-info'; }
                                                        if ($daysDiff > 0) { $badgeClass = 'bg-danger'; }
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    @if(!is_null($row->next_service_date))
                                                        {{ \Carbon\Carbon::parse($row->next_service_date)->diffForHumans(['parts'=>2,'short'=>true,'syntax'=>\Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('service-schedules.send', $row->vehicle_no) }}" onsubmit="return confirm('Send reminder email now?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-envelope"></i> Send Reminder
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No schedules found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $schedules->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>


