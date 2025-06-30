<x-layout>
    <x-slot name="title">Transaction Details - {{ $paymentTransaction->transaction_no }}</x-slot>

    <div class="pagetitle">
        <h1>Transaction Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-transactions.index') }}">Payment Transactions</a></li>
                <li class="breadcrumb-item active">{{ $paymentTransaction->transaction_no }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Main Transaction Details -->
            <div class="col-lg-8">
                <!-- Transaction Header -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="bi bi-receipt"></i> {{ $paymentTransaction->transaction_no }}
                                </h5>
                                <small class="text-muted">
                                    Created {{ $paymentTransaction->created_at->format('M d, Y \a\t h:i A') }}
                                    @if($paymentTransaction->created_by)
                                        by {{ $paymentTransaction->created_by }}
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                @switch($paymentTransaction->status)
                                    @case('draft')
                                        <span class="badge bg-secondary fs-6">
                                            <i class="bi bi-pencil"></i> Draft
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning fs-6">
                                            <i class="bi bi-clock"></i> Pending Approval
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-info fs-6">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle-fill"></i> Completed
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-dark fs-6">
                                            <i class="bi bi-x-circle"></i> Cancelled
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Transaction Type & Amount -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    @if($paymentTransaction->type === 'cash_in')
                                        <div class="bg-success text-white rounded-circle p-3 me-3">
                                            <i class="bi bi-arrow-down-circle fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-success">Cash In (Revenue)</h6>
                                            <h3 class="mb-0 text-success">+Rs. {{ number_format($paymentTransaction->amount, 2) }}</h3>
                                        </div>
                                    @else
                                        <div class="bg-danger text-white rounded-circle p-3 me-3">
                                            <i class="bi bi-arrow-up-circle fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-danger">Cash Out (Expense)</h6>
                                            <h3 class="mb-0 text-danger">-Rs. {{ number_format($paymentTransaction->amount, 2) }}</h3>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="text-muted mb-1">Transaction Date</h6>
                                <h5 class="mb-0">{{ $paymentTransaction->transaction_date->format('F d, Y') }}</h5>
                                <small class="text-muted">{{ $paymentTransaction->transaction_date->format('l') }}</small>
                            </div>
                        </div>

                        <!-- Description & Reference -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="text-muted mb-1">Description</h6>
                                <p class="mb-0">{{ $paymentTransaction->description }}</p>
                            </div>
                            @if($paymentTransaction->reference_no)
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-1">Reference Number</h6>
                                    <p class="mb-0">{{ $paymentTransaction->reference_no }}</p>
                                </div>
                            @endif
                        </div>

                        @if($paymentTransaction->notes)
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Notes</h6>
                                <p class="mb-0">{{ $paymentTransaction->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-credit-card"></i> Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Payment Method</h6>
                                <p class="mb-3">
                                    <span class="badge bg-secondary">{{ $paymentTransaction->paymentMethod->name ?? 'N/A' }}</span>
                                    @if($paymentTransaction->paymentMethod && $paymentTransaction->paymentMethod->description)
                                        <br><small class="text-muted">{{ $paymentTransaction->paymentMethod->description }}</small>
                                    @endif
                                </p>
                            </div>
                            @if($paymentTransaction->bankAccount)
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Bank Account</h6>
                                    <p class="mb-3">
                                        <strong>{{ $paymentTransaction->bankAccount->account_name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $paymentTransaction->bankAccount->bank_name }} - 
                                            {{ $paymentTransaction->bankAccount->account_number }}
                                        </small>
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Category</h6>
                                <p class="mb-0">
                                    <span class="badge bg-info">{{ $paymentTransaction->paymentCategory->name ?? 'N/A' }}</span>
                                    @if($paymentTransaction->paymentCategory && $paymentTransaction->paymentCategory->parent)
                                        <br><small class="text-muted">{{ $paymentTransaction->paymentCategory->parent->name }}</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linked Entities -->
                @if($paymentTransaction->customer || $paymentTransaction->supplier || $paymentTransaction->salesInvoice || $paymentTransaction->purchaseOrder)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-link"></i> Linked Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($paymentTransaction->customer)
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Customer</h6>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success fs-4 me-2"></i>
                                            <div>
                                                <strong>{{ $paymentTransaction->customer->name }}</strong><br>
                                                <small class="text-muted">{{ $paymentTransaction->customer->custom_id }}</small>
                                                @if($paymentTransaction->customer->phone)
                                                    <br><small class="text-muted">{{ $paymentTransaction->customer->phone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($paymentTransaction->supplier)
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Supplier</h6>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-building text-warning fs-4 me-2"></i>
                                            <div>
                                                <strong>{{ $paymentTransaction->supplier->Supp_Name }}</strong><br>
                                                <small class="text-muted">{{ $paymentTransaction->supplier->Supp_CustomID }}</small>
                                                @if($paymentTransaction->supplier->Company_Name)
                                                    <br><small class="text-muted">{{ $paymentTransaction->supplier->Company_Name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($paymentTransaction->salesInvoice || $paymentTransaction->purchaseOrder)
                                <hr>
                                <div class="row">
                                    @if($paymentTransaction->salesInvoice)
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-1">Sales Invoice</h6>
                                            <p class="mb-0">
                                                <a href="#" class="text-decoration-none">
                                                    <i class="bi bi-receipt"></i> Invoice #{{ $paymentTransaction->salesInvoice->invoice_no ?? $paymentTransaction->sales_invoice_id }}
                                                </a>
                                                @if($paymentTransaction->salesInvoice)
                                                    <br><small class="text-muted">
                                                        Total: Rs. {{ number_format($paymentTransaction->salesInvoice->total_amount ?? 0, 2) }}
                                                    </small>
                                                @endif
                                            </p>
                                        </div>
                                    @endif

                                    @if($paymentTransaction->purchaseOrder)
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-1">Purchase Order</h6>
                                            <p class="mb-0">
                                                <a href="#" class="text-decoration-none">
                                                    <i class="bi bi-cart"></i> PO #{{ $paymentTransaction->purchaseOrder->po_No ?? $paymentTransaction->purchase_order_id }}
                                                </a>
                                                @if($paymentTransaction->purchaseOrder)
                                                    <br><small class="text-muted">
                                                        Total: Rs. {{ number_format($paymentTransaction->purchaseOrder->total_amount ?? 0, 2) }}
                                                    </small>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Attachments -->
                @if($paymentTransaction->attachments)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-paperclip"></i> Attachments</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($paymentTransaction->attachments as $attachment)
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                                                <div class="flex-grow-1">
                                                    <small class="d-block">{{ $attachment['name'] }}</small>
                                                    <small class="text-muted">{{ number_format($attachment['size'] / 1024, 1) }} KB</small>
                                                </div>
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-gear"></i> Actions</h6>
                    </div>
                    <div class="card-body">
                        @if(in_array($paymentTransaction->status, ['draft', 'pending']))
                            <a href="{{ route('payment-transactions.edit', $paymentTransaction) }}" 
                               class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-pencil"></i> Edit Transaction
                            </a>
                        @endif

                        @if($paymentTransaction->status === 'pending')
                            <button class="btn btn-success w-100 mb-2 btn-approve" 
                                    data-id="{{ $paymentTransaction->id }}">
                                <i class="bi bi-check"></i> Approve Transaction
                            </button>
                        @endif

                        @if($paymentTransaction->status === 'approved')
                            <button class="btn btn-primary w-100 mb-2 btn-complete" 
                                    data-id="{{ $paymentTransaction->id }}">
                                <i class="bi bi-check-circle"></i> Complete Transaction
                            </button>
                        @endif

                        @if(!in_array($paymentTransaction->status, ['completed', 'cancelled']))
                            <button class="btn btn-danger w-100 mb-2 btn-cancel" 
                                    data-id="{{ $paymentTransaction->id }}">
                                <i class="bi bi-x"></i> Cancel Transaction
                            </button>
                        @endif

                        <a href="{{ route('payment-transactions.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>

                        @if($paymentTransaction->status === 'draft')
                            <hr>
                            <form action="{{ route('payment-transactions.destroy', $paymentTransaction) }}" 
                                  method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> Delete Transaction
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Status Timeline -->
                @if($paymentTransaction->status !== 'draft')
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Status Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Transaction Created</h6>
                                        <small class="text-muted">{{ $paymentTransaction->created_at->format('M d, Y h:i A') }}</small>
                                        @if($paymentTransaction->created_by)
                                            <br><small class="text-muted">by {{ $paymentTransaction->created_by }}</small>
                                        @endif
                                    </div>
                                </div>

                                @if($paymentTransaction->approved_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Transaction Approved</h6>
                                            <small class="text-muted">{{ $paymentTransaction->approved_at->format('M d, Y h:i A') }}</small>
                                            @if($paymentTransaction->approved_by)
                                                <br><small class="text-muted">by {{ $paymentTransaction->approved_by }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($paymentTransaction->status === 'completed')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Transaction Completed</h6>
                                            <small class="text-muted">{{ $paymentTransaction->updated_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                @endif

                                @if($paymentTransaction->status === 'cancelled')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-danger"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Transaction Cancelled</h6>
                                            <small class="text-muted">{{ $paymentTransaction->updated_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Transaction Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Transaction ID:</small>
                            <small><code>{{ $paymentTransaction->id }}</code></small>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Created:</small>
                            <small>{{ $paymentTransaction->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Updated:</small>
                            <small>{{ $paymentTransaction->updated_at->diffForHumans() }}</small>
                        </div>
                        @if($paymentTransaction->is_reconciled)
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Reconciled:</small>
                                <small class="text-success"><i class="bi bi-check-circle"></i> Yes</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
            font-size: 14px;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Transaction actions
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-approve')) {
                e.preventDefault();
                const id = e.target.closest('.btn-approve').dataset.id;
                approveTransaction(id);
            }
            
            if (e.target.closest('.btn-complete')) {
                e.preventDefault();
                const id = e.target.closest('.btn-complete').dataset.id;
                completeTransaction(id);
            }
            
            if (e.target.closest('.btn-cancel')) {
                e.preventDefault();
                const id = e.target.closest('.btn-cancel').dataset.id;
                cancelTransaction(id);
            }
        });

        function approveTransaction(id) {
            Swal.fire({
                title: 'Approve Transaction?',
                text: 'This action cannot be undone.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Approved!', data.success, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }

        function completeTransaction(id) {
            Swal.fire({
                title: 'Complete Transaction?',
                text: 'This will finalize the transaction.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Completed!', data.success, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }

        function cancelTransaction(id) {
            Swal.fire({
                title: 'Cancel Transaction?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Cancelled!', data.success, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-layout> 