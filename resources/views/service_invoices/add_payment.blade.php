<x-layout>
    <x-slot name="title">Add Payment - Service Invoice {{ $serviceInvoice->invoice_no }}</x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Invoice Summary Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>
                            Service Invoice {{ $serviceInvoice->invoice_no }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Customer</h6>
                                <p class="mb-1"><strong>{{ $serviceInvoice->customer->name }}</strong></p>
                                <p class="mb-1 text-muted">{{ $serviceInvoice->customer->phone }}</p>
                                @if($serviceInvoice->vehicle_no)
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ $serviceInvoice->vehicle_no }}</span>
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Invoice Total:</small>
                                        <div class="fw-bold">Rs. {{ number_format($serviceInvoice->grand_total, 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Paid Amount:</small>
                                        <div class="fw-bold text-success">Rs. {{ number_format($serviceInvoice->getTotalPayments(), 2) }}</div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <small class="text-muted">Outstanding:</small>
                                <div class="fs-4 fw-bold text-primary">Rs. {{ number_format($serviceInvoice->getOutstandingAmount(), 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>
                            Add Payment Record
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('service_invoices.store_payment', $serviceInvoice) }}">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $serviceInvoice->customer_id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Payment Amount *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                   id="amount" name="amount" step="0.01" min="0.01" 
                                                   max="{{ $serviceInvoice->getOutstandingAmount() }}"
                                                   value="{{ old('amount', $serviceInvoice->getOutstandingAmount()) }}" required>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Maximum: Rs. {{ number_format($serviceInvoice->getOutstandingAmount(), 2) }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_method_id" class="form-label">Payment Method *</label>
                                        <select class="form-select @error('payment_method_id') is-invalid @enderror" 
                                                id="payment_method_id" name="payment_method_id" required>
                                            <option value="">Select payment method...</option>
                                            <option value="cash" {{ old('payment_method_id') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="card" {{ old('payment_method_id') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                            <option value="bank_transfer" {{ old('payment_method_id') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="cheque" {{ old('payment_method_id') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        </select>
                                        @error('payment_method_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_date" class="form-label">Payment Date *</label>
                                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                               id="payment_date" name="payment_date" 
                                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                        @error('payment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                               id="reference_number" name="reference_number" 
                                               value="{{ old('reference_number') }}"
                                               placeholder="Cheque no, transaction ID, etc.">
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Record Payment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Payments -->
                @if($serviceInvoice->paymentTransactions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Payment History
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceInvoice->paymentTransactions as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $payment->payment_method_id)) }}</span>
                                                </td>
                                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                                <td>{{ $payment->notes ?? '-' }}</td>
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
    </div>

    @push('scripts')
    <script>
        // Auto-calculate full payment
        $('#full_payment_btn').on('click', function() {
            $('#amount').val({{ $serviceInvoice->getOutstandingAmount() }});
        });

        // Validate payment amount
        $('#amount').on('input', function() {
            const amount = parseFloat($(this).val());
            const outstanding = {{ $serviceInvoice->getOutstandingAmount() }};
            
            if (amount > outstanding) {
                $(this).val(outstanding.toFixed(2));
            }
        });
    </script>
    @endpush
</x-layout> 