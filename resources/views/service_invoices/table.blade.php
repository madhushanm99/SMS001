<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Mileage</th>
                        <th>Job Total</th>
                        <th>Parts Total</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>
                                <strong>{{ $invoice->invoice_no }}</strong>
                            </td>
                            <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                            <td>
                                <div>{{ $invoice->customer->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $invoice->customer->phone ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $invoice->vehicle_no ?: 'No Vehicle' }}</span>
                            </td>
                            <td>
                                @if($invoice->mileage)
                                    <span class="badge bg-secondary">{{ number_format($invoice->mileage) }} km</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>Rs. {{ number_format($invoice->job_total, 2) }}</td>
                            <td>Rs. {{ number_format($invoice->parts_total, 2) }}</td>
                            <td>
                                <strong>Rs. {{ number_format($invoice->grand_total, 2) }}</strong>
                            </td>
                            <td>
                                @if($invoice->status === 'hold')
                                    <span class="badge bg-warning">Hold</span>
                                @else
                                    <span class="badge bg-success">Finalized</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->status === 'finalized')
                                    @php $paymentStatus = $invoice->getPaymentStatus(); @endphp
                                    @if($paymentStatus === 'fully_paid')
                                        <span class="badge bg-success">Fully Paid</span>
                                    @elseif($paymentStatus === 'partially_paid')
                                        <span class="badge bg-warning">Partially Paid</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Not Finalized</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('service_invoices.show', $invoice) }}" class="btn btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($invoice->status === 'hold')
                                        <a href="{{ route('service_invoices.edit', $invoice) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    
                                    @if($invoice->status === 'finalized')
                                        <a href="{{ route('service_invoices.pdf', $invoice) }}" target="_blank" class="btn btn-outline-danger" title="Download PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        
                                        <a href="{{ route('service_invoices.add_payment', $invoice) }}" class="btn btn-outline-success" title="Add Payment">
                                            <i class="bi bi-credit-card"></i>
                                        </a>
                                    @endif
                                    
                                    @if($invoice->status === 'hold')
                                        <form method="POST" action="{{ route('service_invoices.destroy', $invoice) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No service invoices found
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($invoices->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div> 