<x-layout>
    <x-slot name="title">Edit Payment Transaction</x-slot>

    <div class="pagetitle">
        <h1>Edit Payment Transaction</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-transactions.index') }}">Payment Transactions</a></li>
                <li class="breadcrumb-item active">Edit #{{ $paymentTransaction->transaction_no }}</li>
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

        <form method="POST" action="{{ route('payment-transactions.update', $paymentTransaction) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="bi bi-receipt"></i> Transaction Details</h6>
                            <span class="badge bg-{{ $paymentTransaction->type === 'cash_in' ? 'success' : 'danger' }}">
                                {{ $paymentTransaction->type === 'cash_in' ? 'Cash In' : 'Cash Out' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label">Transaction Date *</label>
                                    <input type="date" name="transaction_date" id="transaction_date" class="form-control"
                                           value="{{ old('transaction_date', optional($paymentTransaction->transaction_date)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount (LKR) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01"
                                               value="{{ old('amount', $paymentTransaction->amount) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="description" class="form-label">Description *</label>
                                    <input type="text" name="description" id="description" class="form-control"
                                           value="{{ old('description', $paymentTransaction->description) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="reference_no" class="form-label">Reference Number</label>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control"
                                           value="{{ old('reference_no', $paymentTransaction->reference_no) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_method_id" class="form-label">Payment Method *</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-control" required>
                                        <option value="">Select Payment Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}"
                                                {{ old('payment_method_id', $paymentTransaction->payment_method_id) == $method->id ? 'selected' : '' }}>
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
                                                {{ old('bank_account_id', $paymentTransaction->bank_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->account_name }} - {{ $account->bank_name }} ({{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_category_id" class="form-label">Category *</label>
                                    <select name="payment_category_id" id="payment_category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('payment_category_id', $paymentTransaction->payment_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes', $paymentTransaction->notes) }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                @if($paymentTransaction->type === 'cash_in')
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control select2">
                                            <option value="">Select Customer (Optional)</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->custom_id }}"
                                                    {{ old('customer_id', $paymentTransaction->customer_id) == $customer->custom_id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->custom_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sales_invoice_id" class="form-label">Sales Invoice</label>
                                        <input type="number" name="sales_invoice_id" id="sales_invoice_id" class="form-control"
                                               value="{{ old('sales_invoice_id', $paymentTransaction->sales_invoice_id) }}" placeholder="Invoice ID (optional)">
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label for="supplier_id" class="form-label">Supplier</label>
                                        <select name="supplier_id" id="supplier_id" class="form-control select2">
                                            <option value="">Select Supplier (Optional)</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->Supp_CustomID }}"
                                                    {{ old('supplier_id', $paymentTransaction->supplier_id) == $supplier->Supp_CustomID ? 'selected' : '' }}>
                                                    {{ $supplier->Supp_Name }} ({{ $supplier->Supp_CustomID }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                        <input type="number" name="purchase_order_id" id="purchase_order_id" class="form-control"
                                               value="{{ old('purchase_order_id', $paymentTransaction->purchase_order_id) }}" placeholder="PO ID (optional)">
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload receipts or documents (optional)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="{{ route('payment-transactions.show', $paymentTransaction) }}" class="btn btn-secondary w-100">
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
            $('.select2').select2({ width: '100%', allowClear: true });
        });
    </script>
    @endpush
</x-layout>


