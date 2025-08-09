<x-customer.layouts.app :title="'Invoice Details'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Invoice Details - {{ $invoice->invoice_no }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('customer.invoices.download', $invoice) }}" class="btn btn-secondary">
                    <i class="bi bi-download"></i> Download PDF
                </a>
            </div>
            <a href="{{ route('customer.invoices.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
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
        <!-- Invoice Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Invoice Information</h6>
                    <div>
                        <span class="badge bg-{{ $paymentStatusColor }} fs-6">{{ $paymentStatus }}</span>
                        <span class="badge bg-{{ $invoice->status_color }} fs-6 ms-2">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Invoice Number</label>
                            <div class="h5 text-dark">{{ $invoice->invoice_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Invoice Date</label>
                            <div class="h5 text-dark">{{ $invoice->invoice_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted">Notes</label>
                            <div class="text-dark">{{ $invoice->notes ?: 'No notes provided' }}</div>
                        </div>
                    </div>

                    <hr>

                    <!-- Invoice Items -->
                    <h6 class="font-weight-bold text-primary mb-3">Invoice Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
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
                                    <th colspan="4" class="text-end">Grand Total:</th>
                                    <th class="text-end">{{ number_format($invoice->grand_total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->paymentTransactions->count() > 0)
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
                                @foreach($invoice->paymentTransactions as $payment)
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

            <!-- Returns -->
            @if($invoice->returns->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Returns</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Return Date</th>
                                    <th>Return ID</th>
                                    <th>Items Returned</th>
                                    <th>Return Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->returns as $return)
                                <tr>
                                    <td>{{ $return->return_date->format('d M Y') }}</td>
                                    <td>{{ $return->return_no }}</td>
                                    <td>{{ $return->items->count() }} items</td>
                                    <td>{{ number_format($return->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $return->status === 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
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
                    <h6 class="m-0 font-weight-bold text-primary">Payment Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Invoice Total</label>
                            <div class="h5 text-dark">{{ number_format($invoice->grand_total, 2) }}</div>
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

                    @if($totalReturns > 0)
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Returns</label>
                            <div class="h6 text-warning">{{ number_format($totalReturns, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Refunds</label>
                            <div class="h6 text-info">{{ number_format($totalRefunds, 2) }}</div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="text-center">
                        <div class="mb-2">
                            <span class="badge bg-{{ $paymentStatusColor }} fs-6">{{ $paymentStatus }}</span>
                        </div>
                        @if($outstandingAmount > 0)
                            <small class="text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Payment pending: {{ number_format($outstandingAmount, 2) }}
                            </small>
                        @else
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>
                                Invoice fully paid
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
                        <a href="{{ route('customer.invoices.download', $invoice) }}" class="btn btn-secondary">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer.layouts.app>
