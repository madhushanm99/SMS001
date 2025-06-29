<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
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
                    <td colspan="7" class="text-center">No invoices found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $invoices->links() }}
    </div>
</div> 