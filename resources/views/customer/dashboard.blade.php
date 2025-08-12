<x-customer.layouts.app :title="'Dashboard'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Balance Credit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($creditBalance, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($showVehicleQr)
        <div class="col-xl-9 col-md-12 mb-4">
            <div class="card h-100 border-left-warning">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Your Vehicle QR</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($vehicles as $veh)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $veh->vehicle_no }}</div>
                                        <div class="text-muted small">{{ $veh->brand->name ?? '' }} {{ $veh->model }}</div>
                                    </div>
                                    <a href="{{ route('customer.vehicles.qr', $veh) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-qr-code"></i> Download
                                    </a>
                                </div>
                                @php($s = $veh->serviceSchedule)
                                <div class="p-2 rounded" style="background:#fff7ed; border:1px solid #fdba74;">
                                    <div class="small" style="color:#9a3412; font-weight:700;">Next Service (approx.)</div>
                                    <div class="mb-1"><span class="small text-muted">Date:</span> <span style="color:#9a3412; font-weight:700;">{{ $s && $s->next_service_date ? $s->next_service_date->format('d M Y') : '—' }}</span></div>
                                    <div><span class="small text-muted">Mileage:</span> <span style="color:#9a3412; font-weight:700;">{{ $s && $s->next_service_mileage ? number_format($s->next_service_mileage) . ' km' : '—' }}</span></div>
                                    @if(!$s || (!$s->next_service_date && !$s->next_service_mileage))
                                        <div class="text-muted small">no data to show</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Invoices</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>{{ number_format($invoice->grand_total, 2) }}</td>
                                    <td>
                                        @if($invoice->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($invoice->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                                @endforeach

                                @if(count($recentInvoices) === 0)
                                <tr>
                                    <td colspan="5" class="text-center">No invoices found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer.layouts.app>
