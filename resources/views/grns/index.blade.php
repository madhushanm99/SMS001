<x-layout> <x-slot name="title">GRN List</x-slot>
    <div class="mb-4 d-flex justify-content-between">
        <h2 class="h4">Goods Received Notes</h2> <a href="{{ route('grns.create') }}" class="btn btn-primary">+ New
            GRN</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('grns.index') }}" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="GRN No, PO No, Invoice No">
            </div>
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select name="supplier" class="form-select">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->Supp_CustomID }}" {{ request('supplier') == $supplier->Supp_CustomID ? 'selected' : '' }}>
                            {{ $supplier->Supp_Name }} ({{ $supplier->Supp_CustomID }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-secondary w-100">Filter</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('grns.index') }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-sm">
            <thead class="thead-light">
                <tr>
                    <th>GRN No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>PO No</th>
                    <th>Invoice No</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-center">Payment Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grns as $grn)
                    <tr>
                        <td>{{ $grn->grn_no }}</td>
                        <td>{{ $grn->grn_date }}</td>
                        <td>{{ optional($grn->supplier)->Supp_Name ?? $grn->supp_Cus_ID }}</td>
                        <td>{{ $grn->po_No ?? '-' }}</td>
                        <td>{{ $grn->invoice_no ?? '-' }}</td>
                        <td class="text-end">
                            <strong>LKR {{ number_format($grn->total_amount, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                @if($grn->payment_status === 'paid')
                                    <span class="badge bg-success mb-1">Fully Paid</span>
                                @elseif($grn->payment_status === 'partially_paid')
                                    <span class="badge bg-warning mb-1">Partially Paid</span>
                                @else
                                    <span class="badge bg-danger mb-1">Unpaid</span>
                                @endif

                                <small class="text-muted">
                                    Paid: LKR {{ number_format($grn->paid_amount, 2) }}<br>
                                    Balance: LKR {{ number_format($grn->outstanding_amount, 2) }}
                                </small>

                                @if($grn->outstanding_amount > 0)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary mt-1 payment-btn"
                                        data-grn-id="{{ $grn->grn_id }}"
                                        data-grn-no="{{ $grn->grn_no }}"
                                        data-supplier-name="{{ $grn->supp_Cus_ID }}"
                                        data-total-amount="{{ $grn->total_amount }}"
                                        data-outstanding-amount="{{ $grn->outstanding_amount }}"
                                        title="Record Payment">
                                        <i class="bi bi-cash-coin"></i> Pay
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if ($grn->status)
                                <div class="btn-group" role="group">
                                    <a href="{{ route('grns.pdf', $grn->grn_id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary" title="Print PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @php $isFullyPaid = ($grn->outstanding_amount ?? 0) <= 0; @endphp
                                    <a href="{{ $isFullyPaid ? '#' : route('grns.edit', $grn->grn_id) }}"
                                        class="btn btn-sm btn-info {{ $isFullyPaid ? 'disabled' : '' }}" title="{{ $isFullyPaid ? 'Editing disabled for fully paid GRNs' : 'Edit GRN' }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($grn->paymentTransactions->count() > 0)
                                        <button type="button"
                                                class="btn btn-sm btn-success view-payments-btn"
                                                data-grn-id="{{ $grn->grn_id }}"
                                                data-grn-no="{{ $grn->grn_no }}"
                                                title="View Payment History">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('grns.destroy', $grn->grn_id) }}" method="POST"
                                        class="d-inline-block delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete GRN">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                </tr>                 @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No GRNs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $grns->links() }}
    </div>

    <!-- Include Payment Prompt Modal -->
    <x-payment-prompt
    type="grn"
    payment_type="cash_out"
    title="Record Supplier Payment"
    :payment_methods="$paymentMethods"
    :bank_accounts="$bankAccounts"
    :payment_categories="$paymentCategories"
/>

    <!-- Payment History Modal -->
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentHistoryModalLabel">
                        <i class="bi bi-clock-history me-2"></i>Payment History - <span id="historyGrnNo"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="paymentHistoryContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', e => {
                    if (!confirm('Are you sure you want to delete this GRN?')) e.preventDefault();
                });
            });

            // Handle payment button clicks
            $(document).on('click', '.payment-btn', function() {
                const btn = $(this);
                const grnId = btn.data('grn-id');
                const grnNo = btn.data('grn-no');
                const supplierName = btn.data('supplier-name');
                const totalAmount = btn.data('total-amount');
                const outstandingAmount = btn.data('outstanding-amount');

                // Show payment prompt
                showPaymentPrompt({
                    type: 'grn',
                    entity_id: grnId,
                    entity_no: grnNo,
                    party_name: supplierName,
                    total_amount: totalAmount,
                    outstanding_amount: outstandingAmount
                });
            });

            // Handle view payment history button clicks
            $(document).on('click', '.view-payments-btn', function() {
                const btn = $(this);
                const grnId = btn.data('grn-id');
                const grnNo = btn.data('grn-no');

                $('#historyGrnNo').text(grnNo);
                $('#paymentHistoryModal').modal('show');

                // Load payment history
                loadPaymentHistory(grnId);
            });

            function loadPaymentHistory(grnId) {
                $('#paymentHistoryContent').html(`
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

                // You can create an API endpoint to fetch payment history
                // For now, let's create a simple placeholder
                setTimeout(() => {
                    $('#paymentHistoryContent').html(`
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Payment history feature is ready for implementation.
                            You can create an API endpoint to fetch detailed payment records for GRN ID: ${grnId}
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    `);
                }, 1000);
            }

            // Override payment success callback to refresh the page
            window.originalHandlePaymentSuccess = window.handlePaymentSuccess;
            window.handlePaymentSuccess = function(data) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Recorded Successfully!',
                    text: `Payment of Rs. ${parseFloat(data.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})} has been recorded.`,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh the page to show updated payment information
                    window.location.reload();
                });
            };

            // Check for GRN creation flash data and show payment prompt
            @if(session('grn_created'))
                $(document).ready(function() {
                    const grnData = @json(session('grn_created'));

                    if (grnData.prompt_payment) {
                        // Show payment prompt modal
                        showPaymentPrompt({
                            type: 'grn',
                            entity_id: grnData.grn_id,
                            entity_no: grnData.grn_no,
                            party_name: grnData.supplier_name,
                            total_amount: grnData.total_amount,
                            outstanding_amount: grnData.outstanding_amount,
                            available_credit: grnData.available_credit || 0
                        });
                    }
                });
            @endif
        </script>
    @endpush
</x-layout>
