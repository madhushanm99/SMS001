<x-layout>
    <x-slot name="title">Quick Cash In</x-slot>

    <div class="pagetitle">
        <h1>Quick Cash In</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-transactions.index') }}">Payment Transactions</a></li>
                <li class="breadcrumb-item active">Quick Cash In</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-charge"></i> Quick Cash In Entry
                        </h5>
                        <small>Fast entry for incoming payments</small>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('payment-transactions.store') }}" id="quickCashInForm">
                            @csrf
                            <input type="hidden" name="type" value="cash_in">
                            <input type="hidden" name="status" value="completed">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount (LKR) *</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-success text-white">Rs.</span>
                                        <input type="number" name="amount" id="amount" class="form-control" 
                                               step="0.01" min="0.01" value="{{ old('amount') }}" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label">Date *</label>
                                    <input type="date" name="transaction_date" id="transaction_date" 
                                           class="form-control form-control-lg" 
                                           value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <input type="text" name="description" id="description" class="form-control form-control-lg" 
                                       placeholder="What is this payment for?" value="{{ old('description') }}" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_method_id" class="form-label">Payment Method *</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-control form-control-lg" required>
                                        <option value="">Select Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_category_id" class="form-label">Category *</label>
                                    <select name="payment_category_id" id="payment_category_id" class="form-control form-control-lg" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('payment_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Customer Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer (Optional)</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->custom_id }}" {{ old('customer_id') == $customer->custom_id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->custom_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="reference_no" class="form-label">Reference Number</label>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control" 
                                           placeholder="Receipt #, Invoice #, etc." value="{{ old('reference_no') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2" 
                                          placeholder="Additional details...">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <div>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle"></i> Record Cash In
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-lg" id="saveAndNew">
                                        <i class="bi bi-plus-circle"></i> Save & Add Another
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('payment-transactions.dashboard') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Quick Entries -->
                @if($recentTransactions->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Cash In Entries</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_date->format('M d') }}</td>
                                                <td>{{ Str::limit($transaction->description, 30) }}</td>
                                                <td>
                                                    <strong class="text-success">+Rs. {{ number_format($transaction->amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $transaction->paymentMethod->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary btn-duplicate" 
                                                            data-transaction="{{ json_encode([
                                                                'amount' => $transaction->amount,
                                                                'description' => $transaction->description,
                                                                'payment_method_id' => $transaction->payment_method_id,
                                                                'payment_category_id' => $transaction->payment_category_id,
                                                                'customer_id' => $transaction->customer_id,
                                                                'reference_no' => $transaction->reference_no
                                                            ]) }}" title="Duplicate">
                                                        <i class="bi bi-copy"></i>
                                                    </button>
                                                    <a href="{{ route('payment-transactions.show', $transaction) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Type to search customers...",
                allowClear: true,
                width: '100%'
            });

            // Focus on amount field
            $('#amount').focus().select();

            // Save and New functionality
            $('#saveAndNew').on('click', function() {
                const form = $('#quickCashInForm');
                const originalAction = form.attr('action');
                
                // Add redirect parameter
                form.append('<input type="hidden" name="redirect" value="quick-cash-in">');
                form.submit();
            });

            // Duplicate transaction
            $('.btn-duplicate').on('click', function() {
                const data = $(this).data('transaction');
                
                // Populate form with transaction data
                $('#amount').val(data.amount);
                $('#description').val(data.description);
                $('#payment_method_id').val(data.payment_method_id);
                $('#payment_category_id').val(data.payment_category_id);
                $('#customer_id').val(data.customer_id).trigger('change');
                $('#reference_no').val(data.reference_no);
                
                // Scroll to top
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                
                // Focus on amount field
                $('#amount').focus().select();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Transaction Duplicated',
                    text: 'Form has been filled with the selected transaction data.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl + Enter to submit
                if (e.ctrlKey && e.which === 13) {
                    $('#quickCashInForm').submit();
                }
                
                // Ctrl + N for new (save and new)
                if (e.ctrlKey && e.which === 78) {
                    e.preventDefault();
                    $('#saveAndNew').click();
                }
            });

            // Auto-calculate and format amount
            $('#amount').on('input', function() {
                const value = parseFloat($(this).val());
                if (value > 0) {
                    // You could add currency formatting here if needed
                }
            });

            // Form validation
            $('#quickCashInForm').on('submit', function(e) {
                const amount = parseFloat($('#amount').val());
                const description = $('#description').val().trim();
                
                if (!amount || amount <= 0) {
                    e.preventDefault();
                    Swal.fire('Error', 'Please enter a valid amount greater than 0', 'error');
                    $('#amount').focus();
                    return false;
                }
                
                if (!description) {
                    e.preventDefault();
                    Swal.fire('Error', 'Please enter a description for this transaction', 'error');
                    $('#description').focus();
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-layout> 