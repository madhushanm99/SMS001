<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Transaction #</th>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Category</th>
                <th>Entity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>
                        <strong class="text-primary">{{ $transaction->transaction_no }}</strong>
                        @if($transaction->reference_no)
                            <br><small class="text-muted">Ref: {{ $transaction->reference_no }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $transaction->transaction_date->format('M d, Y') }}</span>
                        <br><small class="text-muted">{{ $transaction->transaction_date->format('h:i A') }}</small>
                    </td>
                    <td>
                        @if($transaction->type === 'cash_in')
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-down-circle"></i> Cash In
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-up-circle"></i> Cash Out
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ Str::limit($transaction->description, 40) }}</span>
                        @if($transaction->notes)
                            <br><small class="text-muted">{{ Str::limit($transaction->notes, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold text-{{ $transaction->type === 'cash_in' ? 'success' : 'danger' }}">
                            {{ $transaction->type === 'cash_out' ? '-' : '+' }}{{ number_format($transaction->amount, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $transaction->paymentMethod->name ?? 'N/A' }}</span>
                        @if($transaction->bankAccount)
                            <br><small class="text-muted">{{ $transaction->bankAccount->account_name }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $transaction->paymentCategory->name ?? 'N/A' }}</span>
                        @if($transaction->paymentCategory && $transaction->paymentCategory->parent)
                            <br><small class="text-muted">{{ $transaction->paymentCategory->parent->name }}</small>
                        @endif
                    </td>
                    <td>
                        @if($transaction->customer)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle text-success me-2"></i>
                                <div>
                                    <strong>{{ $transaction->customer->name }}</strong>
                                    <br><small class="text-muted">{{ $transaction->customer->custom_id }}</small>
                                </div>
                            </div>
                        @elseif($transaction->supplier)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building text-warning me-2"></i>
                                <div>
                                    <strong>{{ $transaction->supplier->Supp_Name }}</strong>
                                    <br><small class="text-muted">{{ $transaction->supplier->Supp_CustomID }}</small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @switch($transaction->status)
                            @case('draft')
                                <span class="badge bg-secondary">
                                    <i class="bi bi-pencil"></i> Draft
                                </span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock"></i> Pending
                                </span>
                                @break
                            @case('approved')
                                <span class="badge bg-info">
                                    <i class="bi bi-check-circle"></i> Approved
                                </span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-dark">
                                    <i class="bi bi-x-circle"></i> Cancelled
                                </span>
                                @break
                        @endswitch
                        
                        @if($transaction->created_by)
                            <br><small class="text-muted">by {{ $transaction->created_by }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group-vertical" role="group">
                            <a href="{{ route('payment-transactions.show', $transaction) }}" 
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if(in_array($transaction->status, ['draft', 'pending']))
                                <a href="{{ route('payment-transactions.edit', $transaction) }}" 
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif

                            @if($transaction->status === 'pending')
                                <button class="btn btn-sm btn-outline-success btn-approve" 
                                        data-id="{{ $transaction->id }}" title="Approve">
                                    <i class="bi bi-check"></i>
                                </button>
                            @endif

                            @if($transaction->status === 'approved')
                                <button class="btn btn-sm btn-outline-primary btn-complete" 
                                        data-id="{{ $transaction->id }}" title="Complete">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            @endif

                            @if(!in_array($transaction->status, ['completed', 'cancelled']))
                                <button class="btn btn-sm btn-outline-danger btn-cancel" 
                                        data-id="{{ $transaction->id }}" title="Cancel">
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif

                            @if($transaction->status === 'draft')
                                <form action="{{ route('payment-transactions.destroy', $transaction) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this transaction?')" 
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                            <h5>No transactions found</h5>
                            <p>No payment transactions match your current filters.</p>
                            <a href="{{ route('payment-transactions.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create First Transaction
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($transactions->hasPages())
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} results
        </div>
        
        <nav>
            {{ $transactions->links() }}
        </nav>
    </div>
@endif 