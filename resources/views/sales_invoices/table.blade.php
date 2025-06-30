<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Amount Paid</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $totalPaid = $invoice->paymentTransactions()
                        ->where('status', 'completed')
                        ->where('type', 'cash_in')
                        ->sum('amount');
                    $outstandingAmount = $invoice->grand_total - $totalPaid;
                    $paymentStatus = $totalPaid >= $invoice->grand_total ? 'Paid' : ($totalPaid > 0 ? 'Partially Paid' : 'Unpaid');
                    $paymentStatusColor = $paymentStatus === 'Paid' ? 'success' : ($paymentStatus === 'Partially Paid' ? 'warning' : 'danger');
                    
                    // Debug: Log the values (can be removed in production)
                    \Log::info("Invoice {$invoice->invoice_no}: Total={$invoice->grand_total}, Paid={$totalPaid}, Outstanding={$outstandingAmount}, Status={$paymentStatus}");
                @endphp
                <tr>
                    <td>{{ $invoice->invoice_no }}</td>
                    <td>
                        {{ $invoice->customer ? $invoice->customer->name : 'N/A' }}
                        @if($invoice->customer)
                            <small class="text-muted d-block">{{ $invoice->customer->phone }}</small>
                        @endif
                    </td>
                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                    <td>Rs. {{ number_format($invoice->grand_total, 2) }}</td>
                    <td>
                        <div>Rs. {{ number_format($totalPaid, 2) }}</div>
                        @if($outstandingAmount > 0)
                            <small class="text-danger">
                                Balance: Rs. {{ number_format($outstandingAmount, 2) }}
                            </small>
                        @endif
                        
                        
                    </td>
                    <td>
                        <span class="badge bg-{{ $paymentStatusColor }}">
                            {{ $paymentStatus }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $invoice->status_color }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td>{{ $invoice->created_by }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('sales_invoices.show', $invoice->id) }}" 
                               class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if($invoice->status === 'hold')
                                <a href="{{ route('sales_invoices.edit', $invoice->id) }}" 
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('sales_invoices.finalize_hold', $invoice->id) }}" 
                                   class="btn btn-sm btn-outline-success" title="Finalize"
                                   onclick="return confirm('Are you sure you want to finalize this invoice?')">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            @endif

                            @if($invoice->status === 'finalized' && in_array(auth()->user()->usertype, ['admin', 'manager']))
                                <a href="{{ route('sales_invoices.edit', $invoice->id) }}" 
                                   class="btn btn-sm btn-outline-warning" title="Edit Finalized Invoice">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            @endif

                            @if($invoice->status === 'finalized')
                                <button type="button" 
                                        class="btn btn-sm {{ $paymentStatus === 'Paid' ? 'btn-success' : ($paymentStatus === 'Partially Paid' ? 'btn-warning' : 'btn-outline-info') }} payment-btn" 
                                        title="{{ $paymentStatus === 'Paid' ? 'View/Update Payment Details' : ($paymentStatus === 'Partially Paid' ? 'Add Payment (Balance: Rs. ' . number_format($outstandingAmount, 2) . ')' : 'Record Payment (Rs. ' . number_format($outstandingAmount, 2) . ')') }}"
                                        data-bs-toggle="tooltip"
                                        data-invoice-id="{{ $invoice->id }}"
                                        data-invoice-no="{{ $invoice->invoice_no }}"
                                        data-customer-name="{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}"
                                        data-total-amount="{{ $invoice->grand_total }}"
                                        data-outstanding-amount="{{ $outstandingAmount }}"
                                        data-payment-status="{{ $paymentStatus }}"
                                        data-total-paid="{{ $totalPaid }}"
                                        onclick="handlePaymentClick(this, '{{ $invoice->id }}', '{{ addslashes($invoice->invoice_no) }}', '{{ addslashes($invoice->customer ? $invoice->customer->name : 'N/A') }}', {{ $invoice->grand_total }}, {{ $outstandingAmount }}, 'invoice')">
                                    <i class="bi bi-credit-card"></i>
                                </button>
                                
                                <a href="{{ route('sales_invoices.pdf', $invoice->id) }}" 
                                   class="btn btn-sm btn-outline-info" title="View PDF" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                @if($invoice->customer && $invoice->customer->email)
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            title="Email Invoice" onclick="emailInvoice({{ $invoice->id }})">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                @endif
                            @endif

                            @if($invoice->status === 'hold' || ($invoice->status === 'finalized' && in_array(auth()->user()->usertype, ['admin', 'manager'])))
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        title="{{ $invoice->status === 'finalized' ? 'Delete Finalized Invoice' : 'Delete' }}" 
                                        onclick="confirmDelete({{ $invoice->id }}, '{{ $invoice->status }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No invoices found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $invoices->links() }}
    </div>
</div> 