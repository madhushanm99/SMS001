<x-customer.layouts.app :title="'Service Details'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Service Details - {{ $serviceInvoice->invoice_no }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('customer.services.download', $serviceInvoice) }}" class="btn btn-secondary">
                    <i class="bi bi-download"></i> Download PDF
                </a>
            </div>
            <a href="{{ route('customer.services.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Service History
            </a>
        </div>
    </div>

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Service Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Service Information</h6>
                    <div>
                        @if($serviceInvoice->service_type)
                            <span class="badge bg-{{ $serviceInvoice->getServiceTypeColor() }} fs-6">
                                {{ $serviceInvoice->getServiceTypeLabel() }}
                            </span>
                        @endif
                        <span class="badge bg-{{ $serviceInvoice->status === 'finalized' ? 'success' : 'warning' }} fs-6 ms-2">
                            {{ ucfirst($serviceInvoice->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Invoice Number</label>
                            <div class="h5 text-dark">{{ $serviceInvoice->invoice_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Service Date</label>
                            <div class="h5 text-dark">{{ $serviceInvoice->invoice_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Vehicle</label>
                            <div class="h5 text-dark">
                                {{ $serviceInvoice->vehicle_no }}
                                @if($serviceInvoice->vehicle)
                                    <br><small class="text-muted">
                                        {{ $serviceInvoice->vehicle->brand->name ?? '' }} {{ $serviceInvoice->vehicle->model }}
                                        ({{ $serviceInvoice->vehicle->year_of_manufacture }})
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mileage at Service</label>
                            <div class="h5 text-dark">
                                <span class="badge bg-info fs-6">{{ number_format($serviceInvoice->mileage) }} km</span>
                            </div>
                        </div>
                    </div>

                    @if($serviceInvoice->notes)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted">Service Notes</label>
                            <div class="text-dark">{{ $serviceInvoice->notes }}</div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <!-- Job Items -->
                    @if($jobItems->count() > 0)
                    <h6 class="font-weight-bold text-primary mb-3">Services Performed</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobItems as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->item_name }}</div>
                                        @if($item->jobType)
                                            <small class="text-muted">Code: {{ $item->jobType->jobCustomID }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->discount, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Job Total:</th>
                                    <th class="text-end">{{ number_format($serviceInvoice->job_total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <!-- Spare Parts -->
                    @if($spareItems->count() > 0)
                    <h6 class="font-weight-bold text-primary mb-3">Spare Parts Used</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Part</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spareItems as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->item_name }}</div>
                                        @if($item->product)
                                            <small class="text-muted">Code: {{ $item->product->item_code ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->discount, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Parts Total:</th>
                                    <th class="text-end">{{ number_format($serviceInvoice->parts_total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <!-- Grand Total -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-end">Job Total:</th>
                                    <td class="text-end">{{ number_format($serviceInvoice->job_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end">Parts Total:</th>
                                    <td class="text-end">{{ number_format($serviceInvoice->parts_total, 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <th class="text-end">Grand Total:</th>
                                    <th class="text-end">{{ number_format($serviceInvoice->grand_total, 2) }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($serviceInvoice->paymentTransactions->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceInvoice->paymentTransactions as $payment)
                                <tr>
                                    <td>{{ $payment->transaction_date->format('d M Y, h:i A') }}</td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->type === 'cash_in' ? 'success' : 'danger' }}">
                                            {{ $payment->type === 'cash_in' ? 'Payment' : 'Refund' }}
                                        </span>
                                    </td>
                                    <td class="text-{{ $payment->type === 'cash_in' ? 'success' : 'danger' }}">
                                        {{ $payment->type === 'cash_in' ? '+' : '-' }}{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->notes ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Service Type</label>
                            <div>
                                @if($serviceInvoice->service_type)
                                    <span class="badge bg-{{ $serviceInvoice->getServiceTypeColor() }} fs-6">
                                        {{ $serviceInvoice->getServiceTypeLabel() }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">Not Set</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Mileage</label>
                            <div class="h6 text-info">{{ number_format($serviceInvoice->mileage) }} km</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Service Date</label>
                            <div class="h6 text-dark">{{ $serviceInvoice->invoice_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Invoice Total</label>
                            <div class="h5 text-dark">{{ number_format($serviceInvoice->grand_total, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Total Paid</label>
                            <div class="h5 text-success">{{ number_format($totalPayments, 2) }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Outstanding Amount</label>
                            <div class="h4 text-{{ $outstandingAmount > 0 ? 'danger' : 'success' }}">
                                {{ number_format($outstandingAmount, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        @if($outstandingAmount > 0)
                            <small class="text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Payment pending: {{ number_format($outstandingAmount, 2) }}
                            </small>
                        @else
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>
                                Service fully paid
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.services.download', $serviceInvoice) }}" class="btn btn-secondary">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <button class="btn btn-info" disabled>
                            <i class="bi bi-printer"></i> Print Invoice
                            <small class="d-block">Coming Soon</small>
                        </button>
                        @if($outstandingAmount > 0)
                        <button class="btn btn-success" disabled>
                            <i class="bi bi-credit-card"></i> Make Payment
                            <small class="d-block">Coming Soon</small>
                        </button>
                        @endif
                        <button class="btn btn-warning" disabled>
                            <i class="bi bi-calendar-plus"></i> Book Next Service
                            <small class="d-block">Coming Soon</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer.layouts.app>
