<x-customer.layouts.app :title="'My Invoices'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Invoices</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index') }}">All Invoices</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index', ['payment_status' => 'unpaid']) }}">Unpaid</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index', ['payment_status' => 'partial']) }}">Partially Paid</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index', ['payment_status' => 'paid']) }}">Fully Paid</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index', ['status' => 'hold']) }}">On Hold</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.invoices.index', ['status' => 'finalized']) }}">Finalized</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalInvoices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAmount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPaid, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $creditBalance > 0 ? 'danger' : 'success' }} h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $creditBalance > 0 ? 'danger' : 'success' }} text-uppercase mb-1">
                                Credit Balance</div>
                            <div class="h5 mb-0 font-weight-bold text-{{ $creditBalance > 0 ? 'danger' : 'success' }}">
                                {{ $creditBalance > 0 ? '-' : '' }}{{ number_format(abs($creditBalance), 2) }}
                            </div>
                            <small class="text-muted">
                                @if($creditBalance > 0)
                                    Amount you owe
                                @elseif($creditBalance < 0)
                                    Credit in your favor
                                @else
                                    All settled
                                @endif
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-{{ $creditBalance > 0 ? 'exclamation-triangle' : 'check-circle' }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('customer.invoices.index') }}" class="row g-3">
                <div class="col-md-8">
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="Search by invoice number or notes..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Invoice History</h6>
        </div>
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Outstanding</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <strong>{{ $invoice->invoice_no }}</strong>
                                </td>
                                <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td>{{ number_format($invoice->grand_total, 2) }}</td>
                                <td class="text-success">{{ number_format($invoice->getTotalPayments(), 2) }}</td>
                                <td class="{{ $invoice->getOutstandingAmount() > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($invoice->getOutstandingAmount(), 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $invoice->getPaymentStatusColor() }}">
                                        {{ $invoice->getPaymentStatus() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $invoice->status_color }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.invoices.show', $invoice) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.invoices.download', $invoice) }}"
                                           class="btn btn-sm btn-secondary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} invoices
                        </small>
                    </div>
                    <div>
                        {{ $invoices->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-text display-1 text-muted"></i>
                    <h4 class="mt-3">No Invoices Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'payment_status']))
                            No invoices match your current filters.
                            <a href="{{ route('customer.invoices.index') }}" class="btn btn-link">Clear filters</a>
                        @else
                            You don't have any invoices yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .text-gray-300 {
            color: #dddfeb !important;
        }
    </style>
    @endpush
</x-customer.layouts.app>
