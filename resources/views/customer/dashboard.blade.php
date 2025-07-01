<x-customer.layouts.app :title="'Dashboard'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
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

        <!-- Add more summary cards as needed -->
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