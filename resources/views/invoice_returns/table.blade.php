<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Return No</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Return Date</th>
                <th>Return Amount</th>
                <th>Refund Status</th>
                <th>Status</th>
                <th>Processed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
            <tr>
                <td>
                    <strong>{{ $return->return_no }}</strong>
                </td>
                <td>
                    <a href="{{ route('sales_invoices.show', $return->sales_invoice_id) }}" 
                       class="text-decoration-none" target="_blank">
                        {{ $return->invoice_no }}
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </a>
                </td>
                <td>
                    <div>
                        <strong>{{ $return->customer->name }}</strong><br>
                        <small class="text-muted">{{ $return->customer->phone }}</small>
                    </div>
                </td>
                <td>{{ $return->return_date->format('Y-m-d') }}</td>
                <td>
                    <span class="text-danger fw-bold">-Rs. {{ number_format($return->total_amount, 2) }}</span>
                </td>
                <td>
                    @php
                        $refundTotal = $return->getTotalRefunds();
                        $refundStatus = $return->getRefundStatus();
                        $refundStatusColor = $return->getRefundStatusColor();
                    @endphp
                    <div>
                        <span class="badge bg-{{ $refundStatusColor }} mb-1">
                            {{ ucwords(str_replace('_', ' ', $refundStatus)) }}
                        </span>
                        @if($refundTotal > 0)
                            <br><small class="text-muted">Rs. {{ number_format($refundTotal, 2) }} refunded</small>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="badge bg-{{ $return->status_color }}">
                        {{ ucfirst($return->status) }}
                    </span>
                </td>
                <td>
                    <small>{{ $return->processed_by }}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" 
                                onclick="viewReturn({{ $return->id }})" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" 
                                onclick="printReturn({{ $return->id }})" title="Print PDF">
                            <i class="bi bi-printer"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                    No invoice returns found.
                    <br>
                    <a href="{{ route('invoice_returns.select') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus"></i> Create First Return
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <small class="text-muted">
            Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} results
        </small>
    </div>
    <div>
        {{ $returns->links() }}
    </div>
</div> 