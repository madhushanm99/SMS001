<x-layout> <x-slot name="title">Purchase Returns</x-slot>
    <div class="d-flex justify-content-between mb-3">
        <h4>Purchase Returns</h4> <a href="{{ route('purchase_returns.create') }}" class="btn btn-primary">+ New
            Return</a>
    </div>
    <form method="GET" class="mb-3 row g-2">
        <div class="col-md-4"> <select name="supplier" class="form-control">
                <option value="">-- Filter by Supplier --</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->Supp_CustomID }}"
                        {{ request('supplier') == $s->Supp_CustomID ? 'selected' : '' }}> {{ $s->Supp_Name }} </option>
                    @endforeach
            </select> </div>
        <div class="col-md-4"> <select name="grn" class="form-control">
                <option value="">-- Filter by GRN --</option>
                @foreach ($grns as $g)
                    <option value="{{ $g->grn_no }}" {{ request('grn') == $g->grn_no ? 'selected' : '' }}>
                        {{ $g->grn_no }} </option>
                @endforeach
            </select> </div>
        <div class="col-md-4"> <button class="btn btn-secondary">Apply</button> </div>
    </form>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-sm text-sm">
        <thead class="thead-light">
            <tr>
                <th>Return No</th>
                <th>GRN No</th>
                <th>Supplier</th>
                <th>Total Amount</th>
                <th>Refund Status</th>
                <th>Returned By</th>
                <th>Date</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $pr)
                <tr>
                    <td>{{ $pr->return_no }}</td>
                    <td>{{ $pr->grn_no }}</td>
                    <td>{{ $pr->supplier->Supp_Name ?? $pr->supp_Cus_ID }}</td>
                    <td>LKR {{ number_format($pr->getTotalAmount(), 2) }}</td>
                    <td>
                        @php
                            $paymentStatus = $pr->getPaymentStatus();
                            $totalPayments = $pr->getTotalPayments();
                            $outstanding = $pr->getOutstandingAmount();
                        @endphp
                        
                        @if($paymentStatus == 'paid')
                            <span class="badge badge-success">Refunded</span>
                        @elseif($paymentStatus == 'partially_paid')
                            <span class="badge badge-warning">Partial Refund</span>
                            <small class="d-block text-muted">
                                Refunded: LKR {{ number_format($totalPayments, 2) }}<br>
                                Outstanding: LKR {{ number_format($outstanding, 2) }}
                            </small>
                        @else
                            <span class="badge badge-danger">No Refund</span>
                            <small class="d-block text-muted">
                                Outstanding: LKR {{ number_format($outstanding, 2) }}
                            </small>
                        @endif
                    </td>
                    <td>{{ $pr->returned_by }}</td>
                    <td>{{ $pr->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if ($pr->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Deleted</span>
                            @endif
                    </td>
                    <td class="text-center">
                        <!-- Payment Management Buttons -->
                        @if ($pr->status && $pr->getOutstandingAmount() > 0)
                            <button class="btn btn-sm btn-success" 
                                    onclick="openPaymentModal({{ $pr->id }}, '{{ $pr->return_no }}', {{ $pr->getTotalAmount() }}, {{ $pr->getOutstandingAmount() }}, '{{ $pr->supplier->Supp_Name ?? 'Unknown' }}')"
                                    title="Record Refund">
                                <i class="bi bi-cash-coin"></i> Refund
                            </button>
                        @endif
                        
                        @if ($pr->status && $pr->getTotalPayments() > 0)
                            <button class="btn btn-sm btn-info" 
                                    onclick="viewPaymentHistory({{ $pr->id }})"
                                    title="View Refund History">
                                <i class="bi bi-clock-history"></i>
                            </button>
                        @endif
                        
                        <!-- Standard Action Buttons -->
                        <a href="{{ route('purchase_returns.pdf', $pr->id) }}"
                           class="btn btn-sm btn-outline-dark" 
                           target="_blank"
                           title="View PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        
                        @if ($pr->status)
                            <form action="{{ route('purchase_returns.destroy', $pr->id) }}" method="POST"
                                class="d-inline-block"> 
                                @csrf @method('DELETE') 
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this return?')"
                                        title="Delete Return">
                                    <i class="bi bi-trash"></i>
                                </button> 
                            </form>
                        @endif
                    </td>
            </tr> @empty <tr>
                    <td colspan="9" class="text-center text-muted">No returns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Payment Prompt Modal -->
    @include('components.payment-prompt', [
        'type' => 'purchase_return',
        'payment_type' => 'cash_in',
        'payment_methods' => $payment_methods ?? [],
        'bank_accounts' => $bank_accounts ?? [],
        'payment_categories' => $payment_categories ?? []
    ])

    <!-- Refund History Modal -->
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentHistoryModalLabel">Refund History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentHistoryContent">
                    <!-- Refund history will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set global entity type for payment modal
        window.currentEntityType = 'purchase_return';

        // Global variables for payment system
        let currentPurchaseReturnId = null;
        let currentOutstandingAmount = 0;

        function openPaymentModal(purchaseReturnId, returnNo, totalAmount, outstandingAmount, supplierName) {
            currentPurchaseReturnId = purchaseReturnId;
            
            // Convert amounts to numbers to ensure proper formatting
            const totalAmountNum = parseFloat(totalAmount) || 0;
            const outstandingAmountNum = parseFloat(outstandingAmount) || 0;
            currentOutstandingAmount = outstandingAmountNum;
            
            // Populate modal with purchase return data
            document.getElementById('modal-entity-no').textContent = returnNo;
            document.getElementById('modal-party-name').textContent = supplierName;
            document.getElementById('modal-total-amount').textContent = totalAmountNum.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('modal-outstanding-amount').textContent = outstandingAmountNum.toLocaleString('en-US', {minimumFractionDigits: 2});
            
            // Set the payment amount to outstanding amount by default
            document.getElementById('payment_amount').value = outstandingAmountNum.toFixed(2);
            document.getElementById('payment_amount').setAttribute('max', outstandingAmountNum);
            
            // Show the modal
            $('#paymentPromptModal').modal('show');
        }

        function viewPaymentHistory(purchaseReturnId) {
            $('#paymentHistoryModal').modal('show');
            $('#paymentHistoryContent').html('<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading refund history...</p></div>');
            
            // TODO: Implement API call to fetch refund history
            setTimeout(() => {
                $('#paymentHistoryContent').html('<p class="text-muted">Refund history API integration coming soon...</p>');
            }, 1000);
        }

        function recordPayment() {
            const formData = new FormData(document.getElementById('paymentForm'));
            const submitBtn = document.getElementById('recordPaymentBtn');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
            
            fetch(`/purchase-returns/${currentPurchaseReturnId}/create-payment`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#paymentPromptModal').modal('hide');
                    
                    // Show success message and refresh page
                    Swal.fire({
                        title: 'Refund Recorded!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Refresh the page to show updated refund status
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Refund error:', error);
                Swal.fire('Error', 'An error occurred while processing the refund.', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-cash-coin me-1"></i>Record Refund';
            });
        }

        function skipPayment() {
            $('#paymentPromptModal').modal('hide');
            // For purchase returns, redirect to index with success message
            window.location.href = '/purchase-returns?message=Purchase Return Created Successfully!';
        }

        // Override payment success callback to refresh page instead of redirecting
        window.paymentSuccessCallback = function(data) {
            $('#paymentPromptModal').modal('hide');
            
            Swal.fire({
                title: 'Refund Recorded!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Refresh the page to show updated refund status
                window.location.reload();
            });
        };
    </script>
</x-layout>
