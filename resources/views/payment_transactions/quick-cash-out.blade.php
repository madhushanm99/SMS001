<x-layout>
    <x-slot name="title">Quick Cash Out</x-slot>

    <div class="pagetitle">
        <h1>Quick Cash Out</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-transactions.index') }}">Payment Transactions</a></li>
                <li class="breadcrumb-item active">Quick Cash Out</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-charge"></i> Quick Cash Out Entry
                        </h5>
                        <small>Fast entry for outgoing payments</small>
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

                        <form method="POST" action="{{ route('payment-transactions.store') }}" id="quickCashOutForm">
                            @csrf
                            <input type="hidden" name="type" value="cash_out">
                            <input type="hidden" name="status" value="completed">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount (LKR) *</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-danger text-white">Rs.</span>
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
                                    <label for="bank_account_id" class="form-label">Bank Account</label>
                                    <select name="bank_account_id" id="bank_account_id" class="form-control form-control-lg">
                                        <option value="">Select Bank Account</option>
                                        @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->account_name }} - {{ $account->bank_name }}
                                                (Balance: {{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
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
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label">Supplier (Optional)</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->Supp_CustomID }}" {{ old('supplier_id') == $supplier->Supp_CustomID ? 'selected' : '' }}>
                                                {{ $supplier->Supp_Name }} ({{ $supplier->Supp_CustomID }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="reference_no" class="form-label">Reference Number</label>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control"
                                           placeholder="Check #, Transfer Ref, etc." value="{{ old('reference_no') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="1" placeholder="Additional details...">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <div>
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="bi bi-check-circle"></i> Record Cash Out
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-lg" id="saveAndNew">
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

                @if($recentTransactions->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Cash Out Entries</h6>
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
                                                    <strong class="text-danger">-Rs. {{ number_format($transaction->amount, 2) }}</strong>
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
                                                                'supplier_id' => $transaction->supplier_id,
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
            $('.select2').select2({
                placeholder: "Type to search suppliers...",
                allowClear: true,
                width: '100%'
            });

            $('#saveAndNew').on('click', function() {
                const form = $('#quickCashOutForm');
                form.append('<input type="hidden" name="redirect" value="quick-cash-out">');
                form.submit();
            });

            $('.btn-duplicate').on('click', function() {
                const data = $(this).data('transaction');
                $('#amount').val(data.amount);
                $('#description').val(data.description);
                $('#payment_method_id').val(data.payment_method_id);
                $('#payment_category_id').val(data.payment_category_id);
                $('#supplier_id').val(data.supplier_id).trigger('change');
                $('#reference_no').val(data.reference_no);
                $('html, body').animate({ scrollTop: 0 }, 500);
                $('#amount').focus().select();
                Swal.fire({ icon: 'success', title: 'Transaction Duplicated', timer: 2000, showConfirmButton: false });
            });
        });
    </script>
    @endpush
</x-layout>


