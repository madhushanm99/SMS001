<x-layout>
    <x-slot name="title">{{ $type === 'cash_in' ? 'Record Cash In' : 'Record Cash Out' }}</x-slot>

    <div class="pagetitle">
        <h1>{{ $type === 'cash_in' ? 'Record Cash In' : 'Record Cash Out' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-transactions.index') }}">Payment Transactions</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        @if ($errors->any())
            <div class="alert alert-danger">
                <h6><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Transaction Type Toggle -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                    <a href="{{ route('payment-transactions.create', ['type' => 'cash_in']) }}" 
                       class="btn btn-{{ $type === 'cash_in' ? 'success' : 'outline-success' }}">
                        <i class="bi bi-arrow-down-circle"></i> Cash In (Revenue)
                    </a>
                    <a href="{{ route('payment-transactions.create', ['type' => 'cash_out']) }}" 
                       class="btn btn-{{ $type === 'cash_out' ? 'danger' : 'outline-danger' }}">
                        <i class="bi bi-arrow-up-circle"></i> Cash Out (Expense)
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('payment-transactions.store') }}" 
              enctype="multipart/form-data" id="transaction_form">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="row">
                <!-- Main Form Card -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-receipt"></i> Transaction Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label">Transaction Date *</label>
                                    <input type="date" name="transaction_date" id="transaction_date" 
                                           class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount (LKR) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" name="amount" id="amount" class="form-control" 
                                               step="0.01" min="0.01" value="{{ old('amount', $linkedData['amount'] ?? '') }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Description & Reference -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="description" class="form-label">Description *</label>
                                    <input type="text" name="description" id="description" class="form-control" 
                                           placeholder="Enter transaction description" 
                                           value="{{ old('description', $linkedData['description'] ?? '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="reference_no" class="form-label">Reference Number</label>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control" 
                                           placeholder="Check #, Invoice #, etc." value="{{ old('reference_no') }}">
                                    <small class="text-muted">Required for some payment methods</small>
                                </div>
                            </div>

                            <!-- Payment Method & Bank Account -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_method_id" class="form-label">Payment Method *</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-control" required>
                                        <option value="">Select Payment Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}" 
                                                    data-requires-reference="{{ $method->requires_reference ? 'true' : 'false' }}"
                                                    data-requires-bank="{{ $method->code !== 'CASH' ? 'true' : 'false' }}"
                                                    {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="bank_account_id" class="form-label">Bank Account</label>
                                    <select name="bank_account_id" id="bank_account_id" class="form-control">
                                        <option value="">Select Bank Account</option>
                                        @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->account_name }} - {{ $account->bank_name }}
                                                (Balance: {{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Required for non-cash payments</small>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_category_id" class="form-label">Category *</label>
                                    <select name="payment_category_id" id="payment_category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('payment_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>
                                            Complete Immediately
                                        </option>
                                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>
                                            Pending Approval
                                        </option>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                            Save as Draft
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Entity Selection -->
                            <div class="row mb-3">
                                @if($type === 'cash_in')
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control select2">
                                            <option value="">Select Customer (Optional)</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->custom_id }}" 
                                                        {{ old('customer_id', $linkedData['customer_id'] ?? '') == $customer->custom_id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->custom_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sales_invoice_id" class="form-label">Sales Invoice</label>
                                        <input type="number" name="sales_invoice_id" id="sales_invoice_id" 
                                               class="form-control" placeholder="Invoice ID (if applicable)" 
                                               value="{{ old('sales_invoice_id', request('sales_invoice_id')) }}">
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label for="supplier_id" class="form-label">Supplier</label>
                                        <select name="supplier_id" id="supplier_id" class="form-control select2">
                                            <option value="">Select Supplier (Optional)</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->Supp_CustomID }}" 
                                                        {{ old('supplier_id', $linkedData['supplier_id'] ?? '') == $supplier->Supp_CustomID ? 'selected' : '' }}>
                                                    {{ $supplier->Supp_Name }} ({{ $supplier->Supp_CustomID }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                        <input type="number" name="purchase_order_id" id="purchase_order_id" 
                                               class="form-control" placeholder="PO ID (if applicable)" 
                                               value="{{ old('purchase_order_id', request('purchase_order_id')) }}">
                                    </div>
                                @endif
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                          placeholder="Additional notes or comments">{{ old('notes') }}</textarea>
                            </div>

                            <!-- File Attachments -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control" 
                                       multiple accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload receipts, invoices, or other supporting documents (PDF, Images, Max: 2MB each)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-calculator"></i> Transaction Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Type:</span>
                                <span class="badge bg-{{ $type === 'cash_in' ? 'success' : 'danger' }}">
                                    {{ $type === 'cash_in' ? 'Cash In' : 'Cash Out' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Amount:</span>
                                <strong id="summary_amount">Rs. 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Date:</span>
                                <span id="summary_date">{{ now()->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Payment Method:</span>
                                <span id="summary_method">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Category:</span>
                                <span id="summary_category">-</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Net Impact:</strong>
                                <strong id="summary_impact" class="text-{{ $type === 'cash_in' ? 'success' : 'danger' }}">
                                    {{ $type === 'cash_in' ? '+' : '-' }}Rs. 0.00
                                </strong>
                            </div>

                            @if(!empty($linkedData))
                                <div class="mt-3 p-2 bg-light rounded">
                                    <small class="text-muted d-block">Linked to:</small>
                                    <strong>{{ $linkedData['entity_type'] === 'sales_invoice' ? 'Sales Invoice' : 'Purchase Order' }}</strong>
                                    <br><small>{{ $linkedData['description'] ?? '' }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <button type="submit" class="btn btn-{{ $type === 'cash_in' ? 'success' : 'danger' }} w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Save Transaction
                            </button>
                            <a href="{{ route('payment-transactions.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Type to search...",
                allowClear: true,
                width: '100%'
            });

            // Form validation and dynamic updates
            const form = document.getElementById('transaction_form');
            const amountField = document.getElementById('amount');
            const dateField = document.getElementById('transaction_date');
            const methodField = document.getElementById('payment_method_id');
            const bankField = document.getElementById('bank_account_id');
            const categoryField = document.getElementById('payment_category_id');
            const referenceField = document.getElementById('reference_no');

            // Update summary in real-time
            function updateSummary() {
                const amount = parseFloat(amountField.value) || 0;
                const date = dateField.value ? new Date(dateField.value).toLocaleDateString() : '-';
                const method = methodField.options[methodField.selectedIndex]?.text || '-';
                const category = categoryField.options[categoryField.selectedIndex]?.text || '-';

                document.getElementById('summary_amount').textContent = `Rs. ${amount.toLocaleString('en', {minimumFractionDigits: 2})}`;
                document.getElementById('summary_date').textContent = date;
                document.getElementById('summary_method').textContent = method;
                document.getElementById('summary_category').textContent = category;
                document.getElementById('summary_impact').textContent = `{{ $type === 'cash_in' ? '+' : '-' }}Rs. ${amount.toLocaleString('en', {minimumFractionDigits: 2})}`;
            }

            // Handle payment method changes
            methodField.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const requiresReference = selectedOption?.dataset.requiresReference === 'true';
                const requiresBank = selectedOption?.dataset.requiresBank === 'true';

                // Toggle reference field requirement
                referenceField.required = requiresReference;
                referenceField.parentElement.querySelector('small').textContent = 
                    requiresReference ? 'Required for this payment method' : 'Optional';

                // Toggle bank account requirement
                bankField.required = requiresBank;
                bankField.parentElement.querySelector('small').textContent = 
                    requiresBank ? 'Required for this payment method' : 'Optional';

                updateSummary();
            });

            // Real-time updates
            [amountField, dateField, categoryField].forEach(field => {
                field.addEventListener('input', updateSummary);
                field.addEventListener('change', updateSummary);
            });

            // Initial summary update
            updateSummary();

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const amount = parseFloat(amountField.value);

                // Validate amount
                if (!amount || amount <= 0) {
                    showError(amountField, 'Please enter a valid amount greater than 0');
                    isValid = false;
                }

                // Validate payment method requirements
                const selectedMethod = methodField.options[methodField.selectedIndex];
                if (selectedMethod) {
                    if (selectedMethod.dataset.requiresReference === 'true' && !referenceField.value.trim()) {
                        showError(referenceField, 'Reference number is required for this payment method');
                        isValid = false;
                    }

                    if (selectedMethod.dataset.requiresBank === 'true' && !bankField.value) {
                        showError(bankField, 'Bank account is required for this payment method');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire('Validation Error', 'Please fix the errors and try again', 'error');
                }
            });

            function showError(field, message) {
                field.classList.add('is-invalid');
                
                // Remove existing error
                const existingError = field.parentElement.querySelector('.invalid-feedback');
                if (existingError) existingError.remove();
                
                // Add new error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = message;
                field.parentElement.appendChild(errorDiv);
                
                // Clear error on input
                field.addEventListener('input', function clearError() {
                    field.classList.remove('is-invalid');
                    const error = field.parentElement.querySelector('.invalid-feedback');
                    if (error) error.remove();
                    field.removeEventListener('input', clearError);
                }, { once: true });
            }

            // Trigger initial validation
            methodField.dispatchEvent(new Event('change'));
        });
    </script>
    @endpush
</x-layout> 