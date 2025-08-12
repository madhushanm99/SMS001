<x-layout title="Dashboard">
    <div class="pagetitle">
      <h1>Dashboard</h1>




      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="row" id="service-station-dashboard">
            <!-- Quick Actions -->
            <div class="col-12 mb-4">
                <div class="row">
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('service_invoices.create') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-wrench-adjustable-circle mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">New Service Invoice</div>
                                    <small class="text-muted">Create job & parts</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('appointments.index') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-week mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">Appointments</div>
                                    <small class="text-muted">Manage bookings</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('service-schedules.index') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-bell mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">Service Schedules</div>
                                    <small class="text-muted">Due reminders</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">Customers</div>
                                    <small class="text-muted">Manage customers</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-car-front mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">Vehicles</div>
                                    <small class="text-muted">Manage vehicles</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2 mb-3">
                        <a href="{{ route('payment-transactions.dashboard') }}" class="btn btn-light w-100 p-3 border rounded text-left">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-coin mr-2" style="font-size: 1.25rem;"></i>
                                <div>
                                    <div class="font-weight-bold">Payments</div>
                                    <small class="text-muted">Cash in/out</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            @php
                $today = now()->toDateString();
                $todayAppointments = \App\Models\Appointment::with(['customer', 'vehicle.brand'])
                    ->forDate($today)
                    ->whereTime('appointment_time', '>=', now()->format('H:i:s'))
                    ->orderBy('appointment_time')
                    ->limit(8)
                    ->get();

                $stats = [
                    'pending' => (int) \App\Models\Appointment::pending()->count(),
                    'today_confirmed' => (int) \App\Models\Appointment::forDate($today)->whereIn('status', ['confirmed','completed'])->count(),
                ];
                try { $stats['customers'] = (int) (\App\Models\Customer::count() ?? 0); } catch (\Throwable $e) { $stats['customers'] = 0; }
                try { $stats['vehicles'] = (int) (\App\Models\Vehicle::count() ?? 0); } catch (\Throwable $e) { $stats['vehicles'] = 0; }
            @endphp

            <!-- Main Content: Today's Appointments -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Today's Appointments</h5>
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-list-check"></i> View All
                            </a>
                        </div>

                        @if($todayAppointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Customer</th>
                                            <th>Vehicle</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todayAppointments as $apt)
                                            <tr>
                                                <td><strong>{{ $apt->appointment_no }}</strong></td>
                                                <td><strong>{{ $apt->getFormattedTime() }}</strong></td>
                                                <td>
                                                    <div class="fw-bold">{{ $apt->customer->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $apt->customer->phone ?? '' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $apt->vehicle_no }}</div>
                                                    @if($apt->vehicle)
                                                        <small class="text-muted">{{ $apt->vehicle->brand->name ?? '' }} {{ $apt->vehicle->model }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $apt->getStatusColor() }};">
                                                        {{ $apt->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('appointments.show', $apt) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
                                <div class="mt-2">No appointments scheduled for today</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Widgets -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Quick Stats</h5>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted">Pending</div>
                                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted">Today Done</div>
                                    <div class="h4 mb-0">{{ $stats['today_confirmed'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted">Customers</div>
                                    <div class="h4 mb-0">{{ $stats['customers'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted">Vehicles</div>
                                    <div class="h4 mb-0">{{ $stats['vehicles'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>

  </x-layout>
