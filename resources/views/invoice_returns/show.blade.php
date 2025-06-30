<x-layout title="Return Details">
    <div class="pagetitle">
        <h1>Return Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice_returns.index') }}">Invoice Returns</a></li>
                <li class="breadcrumb-item active">{{ $return->return_no }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Return Header -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1">Return #{{ $return->return_no }}</h4>
                                <p class="text-muted mb-2">Return Date: {{ $return->return_date->format('F d, Y') }}</p>
                                <span class="badge bg-{{ $return->status_color }} fs-6">{{ ucfirst($return->status) }}</span>
                            </div>
                            <div class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('invoice_returns.pdf', $return->id) }}" 
                                       class="btn btn-outline-success" target="_blank">
                                        <i class="bi bi-printer"></i> Print PDF
                                    </a>
                                    <a href="{{ route('invoice_returns.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Return Information -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Return Information</h5>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6>Original Invoice</h6>
                                        <p class="mb-1">
                                            <strong>Invoice No:</strong> 
                                            <a href="{{ route('sales_invoices.show', $return->sales_invoice_id) }}" 
                                               class="text-decoration-none" target="_blank">
                                                {{ $return->invoice_no }}
                                                <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                            </a>
                                        </p>
                                        <p class="mb-1"><strong>Invoice Date:</strong> {{ $return->salesInvoice->invoice_date->format('Y-m-d') }}</p>
                                        <p class="mb-1"><strong>Original Amount:</strong> Rs. {{ number_format($return->salesInvoice->grand_total, 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Return Details</h6>
                                        <p class="mb-1"><strong>Processed By:</strong> {{ $return->processed_by }}</p>
                                        <p class="mb-1"><strong>Return Amount:</strong> 
                                            <span class="text-danger fw-bold">Rs. {{ number_format($return->total_amount, 2) }}</span>
                                        </p>
                                        <p class="mb-1"><strong>Reason:</strong> {{ $return->reason }}</p>
                                    </div>
                                </div>

                                @if($return->notes)
                                <div class="mb-4">
                                    <h6>Additional Notes</h6>
                                    <div class="bg-light p-3 rounded">
                                        {{ $return->notes }}
                                    </div>
                                </div>
                                @endif

                                <!-- Refund Information -->
                                <div class="mb-4">
                                    <h6>Refund Information</h6>
                                    @php
                                        $refundTransactions = $return->paymentTransactions()->with(['paymentMethod', 'bankAccount'])->get();
                                        $totalRefunds = $return->getTotalRefunds();
                                        $refundStatus = $return->getRefundStatus();
                                        $refundStatusColor = $return->getRefundStatusColor();
                                    @endphp
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Refund Status:</strong> 
                                                <span class="badge bg-{{ $refundStatusColor }}">
                                                    {{ ucwords(str_replace('_', ' ', $refundStatus)) }}
                                                </span>
                                            </p>
                                            <p class="mb-1"><strong>Total Refunded:</strong> 
                                                <span class="text-success fw-bold">Rs. {{ number_format($totalRefunds, 2) }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($totalRefunds < $return->total_amount)
                                                <p class="mb-1"><strong>Pending Refund:</strong> 
                                                    <span class="text-warning fw-bold">Rs. {{ number_format($return->total_amount - $totalRefunds, 2) }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    @if($refundTransactions->count() > 0)
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Transaction No</th>
                                                    <th>Date</th>
                                                    <th>Method</th>
                                                    <th>Bank Account</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($refundTransactions as $transaction)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('payment-transactions.show', $transaction->id) }}" 
                                                           class="text-decoration-none" target="_blank">
                                                            {{ $transaction->transaction_no }}
                                                            <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                                    <td>{{ $transaction->paymentMethod->name ?? 'N/A' }}</td>
                                                    <td>{{ $transaction->bankAccount->account_name ?? 'N/A' }}</td>
                                                    <td class="text-end">
                                                        <span class="text-danger">Rs. {{ number_format($transaction->amount, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        No refund transactions found for this return.
                                    </div>
                                    @endif
                                </div>

                                <!-- Return Items -->
                                <h6>Returned Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Qty Returned</th>
                                                <th>Original Qty</th>
                                                <th>Unit Price</th>
                                                <th>Discount</th>
                                                <th>Line Total</th>
                                                <th>Return Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($return->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->item_name }}</strong><br>
                                                    <small class="text-muted">{{ $item->item_id }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger">{{ $item->qty_returned }}</span>
                                                </td>
                                                <td class="text-center">{{ $item->original_qty }}</td>
                                                <td class="text-end">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-center">{{ $item->discount }}%</td>
                                                <td class="text-end">Rs. {{ number_format($item->line_total, 2) }}</td>
                                                <td>
                                                    <small>{{ $item->return_reason ?: 'Not specified' }}</small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-warning">
                                            <tr>
                                                <td colspan="5" class="text-end"><strong>Total Return Amount:</strong></td>
                                                <td class="text-end"><strong>Rs. {{ number_format($return->total_amount, 2) }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Customer Information</h5>
                                
                                <div class="mb-3">
                                    <h6>{{ $return->customer->name }}</h6>
                                    <p class="mb-1"><i class="bi bi-telephone"></i> {{ $return->customer->phone }}</p>
                                    @if($return->customer->email)
                                    <p class="mb-1"><i class="bi bi-envelope"></i> {{ $return->customer->email }}</p>
                                    @endif
                                    @if($return->customer->address)
                                    <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $return->customer->address }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Quick Actions</h5>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('sales_invoices.show', $return->sales_invoice_id) }}" 
                                       class="btn btn-outline-primary" target="_blank">
                                        <i class="bi bi-receipt"></i> View Original Invoice
                                    </a>
                                    <a href="{{ route('customers.show', $return->customer->custom_id) }}" 
                                       class="btn btn-outline-info" target="_blank">
                                        <i class="bi bi-person"></i> View Customer Details
                                    </a>
                                    <a href="{{ route('invoice_returns.pdf', $return->id) }}" 
                                       class="btn btn-outline-success" target="_blank">
                                        <i class="bi bi-file-pdf"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout> 