<x-layout>
    <x-slot name="title">Service Invoice Details</x-slot>
<div class="pagetitle">
    <h1>Service Invoice Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('service_invoices.index') }}">Service Invoices</a></li>
            <li class="breadcrumb-item active">{{ $serviceInvoice->invoice_no }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Invoice Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Invoice {{ $serviceInvoice->invoice_no }}</h5>
                    <div>
                        <span class="badge bg-{{ $serviceInvoice->status === 'finalized' ? 'success' : 'warning' }}">
                            {{ ucfirst($serviceInvoice->status) }}
                        </span>
                        @if($serviceInvoice->status === 'finalized')
                            @php
                                $paymentStatus = $serviceInvoice->getPaymentStatus();
                            @endphp
                            <span class="badge bg-{{ $paymentStatus === 'fully_paid' ? 'success' : ($paymentStatus === 'partially_paid' ? 'warning' : 'danger') }}">
                                {{ ucwords(str_replace('_', ' ', $paymentStatus)) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice No:</strong></td>
                                    <td>{{ $serviceInvoice->invoice_no }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice Date:</strong></td>
                                    <td>{{ $serviceInvoice->invoice_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $serviceInvoice->created_by }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $serviceInvoice->status === 'finalized' ? 'success' : 'warning' }}">
                                            {{ ucfirst($serviceInvoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Customer & Vehicle Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>{{ $serviceInvoice->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer ID:</strong></td>
                                    <td>{{ $serviceInvoice->customer_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $serviceInvoice->customer->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Vehicle No:</strong></td>
                                    <td>{{ $serviceInvoice->vehicle_no ?? 'N/A' }}</td>
                                </tr>
                                @if($serviceInvoice->mileage)
                                <tr>
                                    <td><strong>Mileage:</strong></td>
                                    <td>{{ number_format($serviceInvoice->mileage) }} km</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($serviceInvoice->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Notes</h6>
                            <p class="text-muted">{{ $serviceInvoice->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Invoice Items</h5>
                </div>
                <div class="card-body">
                    @if($serviceInvoice->jobItems->count() > 0)
                    <h6 class="text-primary">Job Types</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Job Type</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceInvoice->jobItems as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-warning">
                                    <th colspan="3">Job Total</th>
                                    <th>Rs. {{ number_format($serviceInvoice->job_total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    @if($serviceInvoice->spareItems->count() > 0)
                    <h6 class="text-info">Spare Parts</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceInvoice->spareItems as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="3">Parts Total</th>
                                    <th>Rs. {{ number_format($serviceInvoice->parts_total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <!-- Grand Total -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-borderless">
                                <tr class="table-success">
                                    <th>Grand Total</th>
                                    <th class="text-end">Rs. {{ number_format($serviceInvoice->grand_total, 2) }}</th>
                                </tr>
                                @if($serviceInvoice->status === 'finalized')
                                <tr>
                                    <td>Total Payments</td>
                                    <td class="text-end">Rs. {{ number_format($serviceInvoice->getTotalPayments(), 2) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <th>Outstanding Amount</th>
                                    <th class="text-end">Rs. {{ number_format($serviceInvoice->getOutstandingAmount(), 2) }}</th>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($serviceInvoice->status === 'finalized' && $serviceInvoice->paymentTransactions->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Transaction No</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceInvoice->paymentTransactions as $payment)
                                <tr>
                                    <td>{{ $payment->transaction_no }}</td>
                                    <td>{{ $payment->transaction_date->format('M d, Y') }}</td>
                                    <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('service_invoices.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                        <div class="btn-group">
                            @if($serviceInvoice->status === 'hold')
                                <a href="{{ route('service_invoices.edit', $serviceInvoice) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('service_invoices.finalize', $serviceInvoice) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to finalize this invoice?')">
                                        <i class="bi bi-check-circle"></i> Finalize
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('service_invoices.pdf', $serviceInvoice) }}" class="btn btn-info" target="_blank">
                                    <i class="bi bi-file-pdf"></i> Download PDF
                                </a>
                                @if($serviceInvoice->getOutstandingAmount() > 0)
                                <a href="{{ route('service_invoices.add_payment', $serviceInvoice) }}" class="btn btn-primary">
                                    <i class="bi bi-credit-card"></i> Add Payment
                                </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</x-layout> 