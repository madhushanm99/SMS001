<x-layout title="Sales Invoices">
    <div class="pagetitle">
        <h1>Sales Invoices</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Sales Invoices</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Sales Invoices</h5>
                            <a href="{{ route('sales_invoices.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create Invoice
                            </a>
                        </div>

                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('sales_invoices.index') }}" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Invoice No, Customer...">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="hold" {{ request('status') == 'hold' ? 'selected' : '' }}>Hold</option>
                                        <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="">All Payments</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date" 
                                           value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date" 
                                           value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        <a href="{{ route('sales_invoices.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Payment Summary Cards -->
                        @php
                            $allInvoices = \App\Models\SalesInvoice::where('status', 'finalized')->with('paymentTransactions')->get();
                            $totalInvoices = $allInvoices->count();
                            $totalAmount = $allInvoices->sum('grand_total');
                            $totalPaid = $allInvoices->sum(function($invoice) {
                                return $invoice->paymentTransactions()
                                    ->where('status', 'completed')
                                    ->where('type', 'cash_in')
                                    ->sum('amount');
                            });
                            $outstandingTotal = $totalAmount - $totalPaid;
                            
                            $paidCount = $allInvoices->filter(function($invoice) {
                                $paid = $invoice->paymentTransactions()
                                    ->where('status', 'completed')
                                    ->where('type', 'cash_in')
                                    ->sum('amount');
                                return $paid >= $invoice->grand_total;
                            })->count();
                            
                            $partiallyPaidCount = $allInvoices->filter(function($invoice) {
                                $paid = $invoice->paymentTransactions()
                                    ->where('status', 'completed')
                                    ->where('type', 'cash_in')
                                    ->sum('amount');
                                return $paid > 0 && $paid < $invoice->grand_total;
                            })->count();
                            
                            $unpaidCount = $totalInvoices - $paidCount - $partiallyPaidCount;
                        @endphp

                        {{-- <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">{{ $paidCount }}</h5>
                                        <p class="card-text text-muted">Fully Paid</p>
                                        <small class="text-success">{{ $totalInvoices > 0 ? round(($paidCount / $totalInvoices) * 100, 1) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">{{ $partiallyPaidCount }}</h5>
                                        <p class="card-text text-muted">Partially Paid</p>
                                        <small class="text-warning">{{ $totalInvoices > 0 ? round(($partiallyPaidCount / $totalInvoices) * 100, 1) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-danger">{{ $unpaidCount }}</h5>
                                        <p class="card-text text-muted">Unpaid</p>
                                        <small class="text-danger">{{ $totalInvoices > 0 ? round(($unpaidCount / $totalInvoices) * 100, 1) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Rs. {{ number_format($outstandingTotal, 0) }}</h5>
                                        <p class="card-text text-muted">Outstanding</p>
                                        <small class="text-muted">Total: Rs. {{ number_format($totalAmount, 0) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Invoices Table -->
                        <div id="invoices-table">
                            @include('sales_invoices.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Are you sure you want to delete this invoice?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Payment Prompt Component -->
    <x-payment-prompt 
        type="invoice" 
        payment_type="cash_in" 
        title="Record Customer Payment"
        :payment_methods="$paymentMethods"
        :bank_accounts="$bankAccounts"
        :payment_categories="$paymentCategories"
    />

    @push('scripts')
    <script>
        function confirmDelete(invoiceId, status = 'hold') {
            const form = document.getElementById('deleteForm');
            form.action = `/sales-invoices/${invoiceId}`;
            
            const modalBody = document.getElementById('deleteModalBody');
            if (status === 'finalized') {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This is a finalized invoice!
                    </div>
                    <p>Are you sure you want to delete this finalized invoice?</p>
                    <p><strong>Note:</strong> Stock quantities will be restored when the invoice is deleted.</p>
                `;
            } else {
                modalBody.innerHTML = 'Are you sure you want to delete this invoice?';
            }
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function emailInvoice(invoiceId) {
            // Show confirmation dialog
            Swal.fire({
                icon: 'question',
                title: 'Send Invoice Email',
                text: 'Are you sure you want to email this invoice to the customer?',
                showCancelButton: true,
                confirmButtonText: 'Send Email',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Sending Email...',
                        text: 'Please wait while we send the invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/sales-invoices/${invoiceId}/email`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Email Sent!',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Email Failed',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to send email. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message
                            });
                        }
                    });
                }
            });
        }

        // Handle payment button click with fallback
        window.handlePaymentClick = function(button, entityId, entityNo, partyName, totalAmount, outstandingAmount, type) {
            
            
            
            
            // Try the main function first
            try {
                window.showPaymentPromptFromIndex(entityId, entityNo, partyName, totalAmount, outstandingAmount, type);
            } catch (error) {
                console.error('Error calling payment prompt:', error);
                
                window.showPaymentPromptFromIndex(
                    btn.data('invoice-id'), 
                    btn.data('invoice-no'), 
                    btn.data('customer-name'), 
                    btn.data('total-amount'), 
                    btn.data('outstanding-amount'), 
                    'invoice'
                );
            }
        };

        // Payment prompt functionality for index page
        window.showPaymentPromptFromIndex = function(entityId, entityNo, partyName, totalAmount, outstandingAmount, type) {
            
            // Set up the payment prompt data
            const data = {
                entity_id: entityId,
                entity_no: entityNo,
                party_name: partyName,
                total_amount: parseFloat(totalAmount) || 0,
                outstanding_amount: parseFloat(outstandingAmount) || 0,
                type: type
            };
        
            // Set global variables for the payment prompt
            window.currentEntityType = type;
            window.currentEntityId = entityId;
            
            // Try to call the function directly first
            if (typeof window.showPaymentPrompt === 'function' && window.paymentPromptReady) {
                window.showPaymentPrompt(data);
                return;
            }
            
            // If not available, wait for it to be defined
            let attempts = 0;
            const maxAttempts = 20; // 4 seconds total
            
            const checkFunction = setInterval(function() {
                attempts++;
                if (typeof window.showPaymentPrompt === 'function' && window.paymentPromptReady) {
                    clearInterval(checkFunction);
                    window.showPaymentPrompt(data);
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkFunction);
                    // Show user-friendly error
                    Swal.fire({
                        icon: 'warning',
                        title: 'Payment System Loading',
                        text: 'The payment system is still loading. Please try again in a moment.',
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#007bff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Try again
                            window.showPaymentPromptFromIndex(entityId, entityNo, partyName, totalAmount, outstandingAmount, type);
                        }
                    });
                }
            }, 200);
        };

        // Override the payment success callback to refresh the page
        window.originalHandlePaymentSuccess = window.handlePaymentSuccess;
        window.handlePaymentSuccess = function(data) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Payment Recorded Successfully!',
                text: `Payment of Rs. ${parseFloat(data.amount).toLocaleString('en-US', {minimumFractionDigits: 2})} has been recorded.`,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                // Refresh the page to show updated payment information
                window.location.reload();
            });
        };

        // Override skip payment to just close modal and refresh
        window.originalSkipPayment = window.skipPayment;
        window.skipPayment = function() {
            $('#paymentPromptModal').modal('hide');
            
            Swal.fire({
                icon: 'info',
                title: 'Payment Skipped',
                text: 'You can record the payment later from this page.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Refresh to show any updated status
                window.location.reload();
            });
        };

        // Initialize tooltips and check payment prompt availability
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });


        });

    </script>
    @endpush
</x-layout> 