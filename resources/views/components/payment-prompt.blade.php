@props([
    'type' => 'invoice', // 'invoice' or 'grn'
    'title' => 'Record Payment',
    'payment_type' => 'cash_in', // 'cash_in' for invoices, 'cash_out' for GRNs
    'payment_methods' => null,
    'bank_accounts' => null,
    'payment_categories' => null
])

<!-- Payment Prompt Modal -->
<div class="modal fade" id="paymentPromptModal" tabindex="-1" aria-labelledby="paymentPromptModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-{{ $payment_type == 'cash_in' ? 'success' : 'primary' }}">
                <h5 class="modal-title text-white" id="paymentPromptModalLabel">
                    <i class="bi bi-cash-coin me-2" id="modal-icon"></i><span id="modal-title-text">{{ $title }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong id="alert-message">{{ $type == 'invoice' ? 'Sales Invoice' : 'GRN' }} Created Successfully!</strong><br>
                    <span id="alert-question">Would you like to record a {{ $payment_type == 'cash_in' ? 'payment from the customer' : 'payment to the supplier' }} now?</span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h6 class="card-title" id="details-title">{{ $type == 'invoice' ? 'Invoice' : 'GRN' }} Details</h6>
                                <p class="mb-1"><strong>Number:</strong> <span id="modal-entity-no"></span></p>
                                <p class="mb-1"><strong id="party-label">{{ $type == 'invoice' ? 'Customer' : 'Supplier' }}:</strong> <span id="modal-party-name"></span></p>
                                <p class="mb-1"><strong>Total Amount:</strong> LKR <span id="modal-total-amount">0.00</span></p>
                                <div id="credit-row" style="display:none;">
                                    <p class="mb-1 text-success"><strong>Supplier Credit:</strong> LKR <span id="modal-available-credit">0.00</span></p>
                                </div>
                                <p class="mb-0"><strong>Outstanding:</strong> LKR <span id="modal-outstanding-amount">0.00</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h6 class="card-title">Payment Summary</h6>
                                <p class="mb-1"><strong>Payment Amount:</strong> LKR <span id="payment-amount-display">0.00</span></p>
                                <p class="mb-1"><strong>Remaining Balance:</strong> LKR <span id="remaining-balance-display">0.00</span></p>
                                <div id="payment-status-badge">
                                    <span class="badge bg-warning">Unpaid</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="paymentForm" novalidate>
                    @csrf
                    <input type="hidden" id="entity-id" name="{{ $type }}_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" class="form-control" id="payment_amount" name="amount"
                                           step="0.01" min="0.01" required>
                                    <button class="btn btn-outline-secondary" type="button" id="fullAmountBtn">Full Amount</button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method_id" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                    <option value="">Select Payment Method</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="bank-account-group" style="display: none;">
                            <div class="mb-3">
                                <label for="bank_account_id" class="form-label">Bank Account <span class="text-danger">*</span></label>
                                <select class="form-select" id="bank_account_id" name="bank_account_id">
                                    <option value="">Select Bank Account</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6" id="category-group">
                            <div class="mb-3">
                                <label for="payment_category_id" class="form-label">Payment Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_category_id" name="payment_category_id" required>
                                    <option value="">Select Category</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <!-- Hidden input for GRN default category -->
                                <input type="hidden" id="grn_default_category" value="5">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference_no" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no"
                                       placeholder="Check #, Transfer ID, etc.">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="payment_description" name="description"
                                       placeholder="Optional payment description">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Fully Paid Message (Hidden by default) -->
                <div id="fullyPaidMessage" style="display: none;">
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="mb-2">Payment Complete!</h5>
                        <p class="mb-0">This {{ $type == 'invoice' ? 'invoice' : 'GRN' }} has been fully paid. No additional payments are required.</p>
                    </div>

                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Payment Status: Completed</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total Amount:</strong> LKR <span id="completed-total-amount">0.00</span></p>
                                    <p class="mb-1"><strong>Amount Paid:</strong> LKR <span id="completed-paid-amount">0.00</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Remaining Balance:</strong> LKR 0.00</p>
                                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Fully Paid</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Normal Payment Buttons (shown when payment is needed) -->
                <div id="paymentButtons">
                    <button type="button" class="btn btn-secondary" id="skipPaymentBtn">
                        <i class="bi bi-x-lg me-1"></i>Skip Payment
                    </button>
                    <button type="button" class="btn btn-success" id="recordPaymentBtn">
                        <i class="bi bi-cash-coin me-1"></i>Record Payment
                    </button>
                </div>

                <!-- Fully Paid Button (hidden by default) -->
                <div id="fullyPaidButtons" style="display: none;">
                    <button type="button" class="btn btn-primary" id="closeModalBtn" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
try {
    // Global variables for payment prompt
    window.currentEntityType = '{{ $type }}';
    window.currentPaymentType = '{{ $payment_type }}';
    window.currentRouteTemplate = '';
    window.currentInvoiceData = null;

    // Update labels based on entity type
    window.updateEntityLabels = function() {
        const entityType = window.currentEntityType || '{{ $type }}';

        if (entityType === 'purchase_return') {
            $('#details-title').text('Purchase Return Details');
            $('#party-label').text('Supplier:');
            $('#alert-message').text('Purchase Return Created Successfully!');
            $('#alert-question').text('Would you like to record a refund from the supplier now?');
        } else if (entityType === 'grn') {
            $('#details-title').text('GRN Details');
            $('#party-label').text('Supplier:');
            $('#alert-message').text('GRN Created Successfully!');
            $('#alert-question').text('Would you like to record a payment to the supplier now?');
        } else if (entityType === 'invoice') {
            $('#details-title').text('Invoice Details');
            $('#party-label').text('Customer:');
            $('#alert-message').text('Sales Invoice Created Successfully!');
            $('#alert-question').text('Would you like to record a payment from the customer now?');
        }
    };

    // Backend data (with null checking)
    window.backendPaymentMethods = @json($payment_methods ?? []);
    window.backendBankAccounts = @json($bank_accounts ?? []);
    window.backendPaymentCategories = @json($payment_categories ?? []);

    // Helper functions
    window.populatePaymentMethods = function(methods) {
        const $paymentMethodSelect = $('#payment_method_id');
        $paymentMethodSelect.empty().append('<option value="">Select Payment Method</option>');

        methods.forEach(function(method) {
            if (method.is_active !== false) {
                $paymentMethodSelect.append(
                    `<option value="${method.id}" data-requires-bank="${method.name === 'Bank Transfer' ? 'true' : 'false'}">${method.name}</option>`
                );
            }
        });

        // Add event listeners
        attachPaymentMethodChangeHandlers($paymentMethodSelect);
    };

    window.populateBankAccounts = function(accounts) {
        const $bankAccountSelect = $('#bank_account_id');
        $bankAccountSelect.empty().append('<option value="">Select Bank Account</option>');

        accounts.forEach(function(account) {
            $bankAccountSelect.append(
                `<option value="${account.id}">${account.account_name} - ${account.bank_name}</option>`
            );
        });
    };

    window.attachPaymentMethodChangeHandlers = function($select) {
        $select.off('change.bankAccount').on('change.bankAccount', function() {
            const selectedOption = $(this).find('option:selected');
            const requiresBank = selectedOption.data('requires-bank') === 'true';
            const methodName = selectedOption.text();

            const isBankTransfer = requiresBank || methodName.toLowerCase().includes('bank transfer');

            if (isBankTransfer) {
                $('#bank-account-group').show();
                $('#bank_account_id').attr('required', true);
            } else {
                $('#bank-account-group').hide();
                $('#bank_account_id').removeAttr('required').val('');
            }
        });
    };

    window.loadBankAccountsFromAPI = function() {
        fetch('/bank-accounts/', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                populateBankAccounts(data.data);
            }
        })
        .catch(error => {
            console.error('Failed to load bank accounts:', error);
            const $bankAccountSelect = $('#bank_account_id');
            $bankAccountSelect.empty().append('<option value="">Select Bank Account</option>');
            $bankAccountSelect.append('<option value="1">Main Business Account</option>');
        });
    };

    window.handlePaymentCategories = function() {
        if (window.currentEntityType === 'invoice') {
            $('#category-group').show();
            $('#payment_category_id').attr('required', true).val('2');
            if ($('#backup_category_id').length === 0) {
                $('#paymentForm').append(`<input type="hidden" id="backup_category_id" name="payment_category_id" value="2">`);
            }
        } else if (window.currentEntityType === 'grn' || window.currentEntityType === 'purchase_return') {
            $('#category-group').hide();
            $('#payment_category_id').removeAttr('required').val('5');
            if ($('#backup_grn_category_id').length === 0) {
                $('#paymentForm').append(`<input type="hidden" id="backup_grn_category_id" name="payment_category_id" value="5">`);
            }
        }
    };

    // Global functions for payment prompt
    window.loadPaymentOptions = function() {
        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            // Wait for jQuery to be available
            const checkJquery = setInterval(function() {
                if (typeof $ !== 'undefined') {
                    clearInterval(checkJquery);
                    executeLoadPaymentOptions();
                }
            }, 100);
        } else {
            executeLoadPaymentOptions();
        }

                function executeLoadPaymentOptions() {
            // First try to load from backend data
            if (window.backendPaymentMethods && Array.isArray(window.backendPaymentMethods) && window.backendPaymentMethods.length > 0) {
                populatePaymentMethods(window.backendPaymentMethods);

                // Load bank accounts from backend
                if (window.backendBankAccounts && Array.isArray(window.backendBankAccounts) && window.backendBankAccounts.length > 0) {
                    populateBankAccounts(window.backendBankAccounts);
                } else {
                    loadBankAccountsFromAPI();
                }

                // Handle payment categories
                handlePaymentCategories();

                return;
            }

            // Fallback to API if backend data not available
            fetch('/payment-methods/', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const $paymentMethodSelect = $('#payment_method_id');
                        $paymentMethodSelect.empty().append('<option value="">Select Payment Method</option>');

                        if (data.success && data.data) {
                        data.data.forEach(function(method) {
                            if (method.is_active) {
                                $paymentMethodSelect.append(
                                    `<option value="${method.id}" data-requires-bank="${method.name === 'Bank Transfer' ? 'true' : 'false'}">${method.name}</option>`
                                );
                            }
                        });

                        // Add event listener for payment method change (ensure it's attached after options are loaded)
                        $paymentMethodSelect.off('change.bankAccount').on('change.bankAccount', function() {
                            const selectedOption = $(this).find('option:selected');
                            const requiresBank = selectedOption.data('requires-bank') === 'true';
                            const methodName = selectedOption.text();

                            // Multiple ways to check for Bank Transfer
                            const isBankTransfer = requiresBank || methodName.toLowerCase().includes('bank transfer');

                            if (isBankTransfer) {
                                $('#bank-account-group').show();
                                document.getElementById('bank-account-group').style.display = 'block';
                                $('#bank_account_id').attr('required', true);
                            } else {
                                $('#bank-account-group').hide();
                                document.getElementById('bank-account-group').style.display = 'none';
                                $('#bank_account_id').removeAttr('required').val('');
                            }
                        });

                        // Additional immediate handler
                        $paymentMethodSelect.on('change', function() {
                            const selectedText = $(this).find('option:selected').text();

                            if (selectedText.toLowerCase().includes('bank transfer')) {
                                $('#bank-account-group').show();
                                document.getElementById('bank-account-group').style.display = 'block';
                                $('#bank_account_id').attr('required', true);
                            }
                        });
                    }

                    // Load bank accounts
                    console.log('Loading bank accounts...');
                    fetch('/bank-accounts/', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            console.log('Bank accounts response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Bank accounts data:', data);
                            const $bankAccountSelect = $('#bank_account_id');
                            $bankAccountSelect.empty().append('<option value="">Select Bank Account</option>');

                            if (data.success && data.data) {
                                data.data.forEach(function(account) {
                                    $bankAccountSelect.append(
                                        `<option value="${account.id}">${account.account_name} - ${account.bank_name}</option>`
                                    );
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Failed to load bank accounts:', error);
                            const $bankAccountSelect = $('#bank_account_id');
                            $bankAccountSelect.empty().append('<option value="">Select Bank Account</option>');
                            // Add a default bank account as fallback
                            $bankAccountSelect.append('<option value="1">Main Business Account</option>');
                        });

                    // For invoices, automatically handle payment category
                    if (window.currentEntityType === 'invoice') {
                        // Simple fallback first - set to Customer Payments (ID 2) immediately
                        $('#payment_category_id').val('2');

                        // Add hidden backup field immediately
                        if ($('#backup_category_id').length === 0) {
                            $('#paymentForm').append(`<input type="hidden" id="backup_category_id" name="payment_category_id" value="2">`);
                        }

                        // Still try to load categories properly, but don't rely on it
                        const categoryType = 'income';

                        fetch(`/payment-categories/?type=${categoryType}`, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.data && data.data.length > 0) {
                                    // Try to find the best category but don't worry if we can't
                                    let customerPaymentsCategory = null;

                                    // 1. Look for exact match: "Customer Payments"
                                    customerPaymentsCategory = data.data.find(category =>
                                        category.name.toLowerCase() === 'customer payments'
                                    );

                                    // 2. Look for any customer payment related category
                                    if (!customerPaymentsCategory) {
                                        customerPaymentsCategory = data.data.find(category =>
                                            category.name.toLowerCase().includes('customer') &&
                                            category.name.toLowerCase().includes('payment')
                                        );
                                    }

                                    if (customerPaymentsCategory) {
                                        // Update to the correct ID if different from 2
                                        $('#payment_category_id').val(customerPaymentsCategory.id);
                                        $('#backup_category_id').val(customerPaymentsCategory.id);
                                    }
                                }
                            })
                            .catch(error => {
                                // Silently handle errors - fallback is already in place
                            });
                    } else if (window.currentEntityType === 'grn') {
                        // For GRN payments, hide category selector and set default to Supplier Payments (ID: 5)
                        $('#category-group').hide();
                        $('#payment_category_id').val('5');

                        // Add hidden backup field for GRN payments
                        if ($('#backup_grn_category_id').length === 0) {
                            $('#paymentForm').append(`<input type="hidden" id="backup_grn_category_id" name="payment_category_id" value="5">`);
                        }
                    } else {
                        // Load payment categories normally for other entities
                        const categoryType = window.currentPaymentType === 'cash_in' ? 'income' : 'expense';
                        fetch(`/payment-categories/?type=${categoryType}`, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                const $categorySelect = $('#payment_category_id');
                                $categorySelect.empty().append('<option value="">Select Category</option>');

                                if (data.success && data.data) {
                                    data.data.forEach(function(category) {
                                        if (category.type === categoryType && category.is_active) {
                                            $categorySelect.append(
                                                `<option value="${category.id}">${category.description || category.name}</option>`
                                            );
                                        }
                                    });
                                }
                            })
                            .catch(error => console.error('Failed to load payment categories:', error));
                    }
                })
                .catch(error => {
                    console.error('Failed to load payment methods:', error);
                    const $paymentMethodSelect = $('#payment_method_id');
                    $paymentMethodSelect.empty().append('<option value="">Select Payment Method</option>');
                    // Add a few default payment methods as fallback
                    $paymentMethodSelect.append('<option value="1">Cash</option>');
                    $paymentMethodSelect.append('<option value="2" data-requires-bank="true">Bank Transfer</option>');
                    $paymentMethodSelect.append('<option value="3">Credit Card</option>');
                    $paymentMethodSelect.append('<option value="4">Check</option>');

                    // Add event listener for payment method change (fallback)
                    attachPaymentMethodChangeHandlers($paymentMethodSelect);

                    // Load bank accounts fallback
                    const $bankAccountSelect = $('#bank_account_id');
                    $bankAccountSelect.empty().append('<option value="">Select Bank Account</option>');
                    $bankAccountSelect.append('<option value="1">Main Business Account</option>');

                    // Handle payment categories
                    handlePaymentCategories();
                });
        }
    };
} catch (error) {
    console.error('Error in payment prompt initial setup:', error);
}

try {
    window.updatePaymentCalculations = function() {
        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            // Wait for jQuery to be available
            const checkJquery = setInterval(function() {
                if (typeof $ !== 'undefined') {
                    clearInterval(checkJquery);
                    executeUpdateCalculations();
                }
            }, 100);
        } else {
            executeUpdateCalculations();
        }

        function executeUpdateCalculations() {
            const paymentAmount = parseFloat($('#payment_amount').val()) || 0;
            const outstandingAmount = parseFloat($('#modal-outstanding-amount').text().replace(/,/g, '')) || 0;
            const totalAmount = parseFloat($('#modal-total-amount').text().replace(/,/g, '')) || 0;
            const availableCredit = parseFloat($('#modal-available-credit').text().replace(/,/g, '')) || 0;


            // Check if invoice is already fully paid from the start
            const isAlreadyFullyPaid = outstandingAmount <= 0;

            if (isAlreadyFullyPaid) {
                console.log('🎉 Invoice is fully paid - showing completion interface');

                // Show fully paid interface
                $('#paymentForm').hide();
                $('#fullyPaidMessage').show();
                $('#paymentButtons').hide();
                $('#fullyPaidButtons').show();

                // Force hide with CSS (backup method)
                $('#paymentForm').css('display', 'none');
                $('#fullyPaidMessage').css('display', 'block');
                $('#paymentButtons').css('display', 'none');
                $('#fullyPaidButtons').css('display', 'block');

                // Update fully paid message amounts
                $('#completed-total-amount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#completed-paid-amount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));

                // Update payment status badge to show fully paid
                $('#payment-status-badge').html('<span class="badge bg-success">Fully Paid</span>');

                // Update the header alert message
                const isFromIndex = window.location.pathname.includes('/sales-invoices') && !window.location.pathname.includes('/create');
                $('.alert-info').removeClass('alert-info').addClass('alert-success').html(`
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Payment Complete!</strong><br>
                    This ${window.currentEntityType === 'invoice' ? 'invoice' : 'GRN'} has been fully paid. ${isFromIndex ? '' : 'No additional payments are required.'}
                `);

                // Update modal title and icon
                $('#modal-title-text').text('Payment Complete');
                $('#modal-icon').removeClass('bi-cash-coin').addClass('bi-check-circle-fill');

                return; // Exit early for fully paid invoices
            }



            // Normal payment interface for unpaid/partially paid invoices
            $('#paymentForm').show();
            $('#fullyPaidMessage').hide();
            $('#paymentButtons').show();
            $('#fullyPaidButtons').hide();

            // Force show/hide with CSS (backup method)
            $('#paymentForm').css('display', 'block');
            $('#fullyPaidMessage').css('display', 'none');
            $('#paymentButtons').css('display', 'block');
            $('#fullyPaidButtons').css('display', 'none');

            // Reset modal title and icon
            $('#modal-title-text').text('Record Payment');
            $('#modal-icon').removeClass('bi-check-circle-fill').addClass('bi-cash-coin');

            // Reset header alert message
            const isFromIndex = window.location.pathname.includes('/sales-invoices') && !window.location.pathname.includes('/create');
            $('.alert-success').removeClass('alert-success').addClass('alert-info').html(`
                <i class="bi bi-info-circle me-2"></i>
                <strong>${isFromIndex ? 'Payment Update' : (window.currentEntityType === 'invoice' ? 'Sales Invoice' : 'GRN') + ' Created Successfully!'}</strong><br>
                ${isFromIndex ? 'Record or update payment for this ' + (window.currentEntityType === 'invoice' ? 'invoice' : 'GRN') + '.' : 'Would you like to record a ' + (window.currentPaymentType === 'cash_in' ? 'payment from the customer' : 'payment to the supplier') + ' now?'}
            `);

            const remainingBalance = outstandingAmount - paymentAmount;

            $('#payment-amount-display').text(paymentAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
            $('#remaining-balance-display').text(Math.max(0, remainingBalance).toLocaleString('en-US', {minimumFractionDigits: 2}));

            // Update payment status badge
            let statusBadge = '';
            if (remainingBalance <= 0 && paymentAmount > 0) {
                statusBadge = '<span class="badge bg-success">Fully Paid</span>';
            } else if (paymentAmount > 0) {
                statusBadge = '<span class="badge bg-warning">Partially Paid</span>';
            } else {
                statusBadge = '<span class="badge bg-danger">Unpaid</span>';
            }
            $('#payment-status-badge').html(statusBadge);
        }
    };
} catch (error) {
    console.error('Error defining updatePaymentCalculations:', error);
}

try {
    window.recordPayment = function() {
        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            // Wait for jQuery to be available
            const checkJquery = setInterval(function() {
                if (typeof $ !== 'undefined') {
                    clearInterval(checkJquery);
                    executeRecordPayment();
                }
            }, 100);
        } else {
            executeRecordPayment();
        }

        function executeRecordPayment() {
            const $form = $('#paymentForm');

            // Special handling for invoices - ensure category is set
            if (window.currentEntityType === 'invoice') {
                let categoryValue = $('#payment_category_id').val();
                const backupCategoryValue = $('#backup_category_id').val();

                // Use backup if main is empty
                if (!categoryValue && backupCategoryValue) {
                    categoryValue = backupCategoryValue;
                    $('#payment_category_id').val(backupCategoryValue);
                }

                if (!categoryValue) {
                    // Try to set a default income category for customer payments
                    const incomeOptions = $('#payment_category_id option').filter(function() {
                        const text = $(this).text().toLowerCase();
                        return text.includes('customer') || text.includes('sales') || text.includes('income');
                    });

                    if (incomeOptions.length > 0) {
                        const defaultValue = incomeOptions.first().val();
                        $('#payment_category_id').val(defaultValue);
                    } else {
                        // Try any income option
                        const anyOption = $('#payment_category_id option:not([value=""])').first();
                        if (anyOption.length) {
                            const defaultValue = anyOption.val();
                            $('#payment_category_id').val(defaultValue);
                        } else {
                            // Force create a hidden field with value 2 (Customer Payments category ID)
                            if ($('#emergency_category_id').length === 0) {
                                $('#paymentForm').append(`<input type="hidden" id="emergency_category_id" name="payment_category_id" value="2">`);
                            }
                        }
                    }
                }
            }

            const formData = new FormData($form[0]);

            // Clear previous validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Disable submit button
            const $submitBtn = $('#recordPaymentBtn');
            $submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Processing...');

            fetch(window.currentRouteTemplate, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.status === 422) {
                    // Handle validation errors
                    return response.json().then(errorData => {
                        // Show validation errors
                        if (errorData.errors) {
                            let errorMessage = 'Validation errors:\n';
                            for (const field in errorData.errors) {
                                errorMessage += `${field}: ${errorData.errors[field][0]}\n`;
                            }
                            throw new Error(errorMessage);
                        } else {
                            throw new Error('Validation failed: ' + (errorData.message || 'Unknown error'));
                        }
                    });
                } else if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close modal first
                    $('#paymentPromptModal').modal('hide');

                    // Show payment success and ask about PDF
                    // Determine entity type and appropriate actions
                    const entityType = window.currentEntityType || 'invoice';
                    const isGRN = entityType === 'grn';
                    const entityData = window.currentInvoiceData;

                    // Configure dialog based on entity type
                    const dialogConfig = {
                        icon: 'success',
                        title: 'Payment Recorded Successfully!',
                        html: `
                            <div class="text-start">
                                <p><strong>Transaction #:</strong> ${data.transaction_no}</p>
                                <p><strong>Remaining Balance:</strong> LKR ${data.remaining_balance.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                                <div class="text-center mt-3">
                                    ${data.is_fully_paid ? '<span class="badge bg-success fs-6">Fully Paid</span>' : '<span class="badge bg-warning fs-6">Partially Paid</span>'}
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: `<i class="bi bi-file-pdf me-1"></i>View ${isGRN ? 'GRN' : 'Invoice'} PDF`,
                        cancelButtonText: `<i class="bi bi-list me-1"></i>Go to ${isGRN ? 'GRN' : 'Invoice'} List`,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d'
                    };

                    // Only show email option for invoices
                    if (!isGRN) {
                        dialogConfig.showDenyButton = true;
                        dialogConfig.denyButtonText = '<i class="bi bi-envelope me-1"></i>Email Invoice';
                        dialogConfig.denyButtonColor = '#28a745';
                    }

                    Swal.fire(dialogConfig).then((result) => {
                        if (result.isConfirmed && entityData) {
                            // Open PDF in new tab - use appropriate URL
                            const pdfUrl = isGRN ? `/grns/${entityData.entity_id}/pdf` : `/sales-invoices/${entityData.entity_id}/pdf`;
                            window.open(pdfUrl, '_blank');
                        } else if (result.isDenied && entityData && !isGRN) {
                            // Email invoice (only for invoices)
                            emailInvoice(entityData.entity_id);
                        }
                        // Redirect to appropriate list page
                        setTimeout(() => {
                            const redirectUrl = isGRN ? '/grns' : '/sales-invoices';
                            window.location.href = redirectUrl;
                        }, 1000);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Handle 422 validation errors
                if (error.message.includes('422')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check all required fields are filled correctly.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while recording the payment: ' + error.message
                    });
                }
            })
            .finally(() => {
                // Re-enable submit button
                $submitBtn.prop('disabled', false).html('<i class="bi bi-cash-coin me-1"></i>Record Payment');
            });
        }
    };
} catch (error) {
    console.error('Error defining recordPayment:', error);
}

try {
    window.skipPayment = function() {
        // Close payment modal
        $('#paymentPromptModal').modal('hide');

        // Determine entity type and appropriate redirect
        const entityType = window.currentEntityType || 'invoice';
        let title, text, redirectUrl;

        if (entityType === 'grn') {
            title = 'GRN Created Successfully!';
            text = 'You can record payment later from the GRN list.';
            redirectUrl = '/grns';
        } else {
            title = 'Invoice Created Successfully!';
            text = 'You can record payment later from the invoice list.';
            redirectUrl = '/sales-invoices';
        }

        // Show simple success alert with action button
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonText: `<i class="bi bi-list me-1"></i>Go to ${entityType === 'grn' ? 'GRN' : 'Invoice'} List`,
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = redirectUrl;
            }
        });
    };
} catch (error) {
    console.error('Error defining skipPayment:', error);
}

// Helper function for emailing invoice
function emailInvoice(invoiceId) {
    fetch(`/sales-invoices/${invoiceId}/email`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: data.message,
                confirmButtonText: '<i class="bi bi-list me-1"></i>Go to Invoice List',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.href = '/sales-invoices';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Email Failed',
                text: data.message,
                confirmButtonText: '<i class="bi bi-list me-1"></i>Go to Invoice List',
                confirmButtonColor: '#6c757d'
            }).then(() => {
                window.location.href = '/sales-invoices';
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to send email. Please try again.',
            confirmButtonText: '<i class="bi bi-list me-1"></i>Go to Invoice List',
            confirmButtonColor: '#6c757d'
        }).then(() => {
            window.location.href = '/sales-invoices';
        });
    });
}

// Document ready events
try {
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        // Wait for jQuery to be available
        const checkJquery = setInterval(function() {
            if (typeof $ !== 'undefined') {
                clearInterval(checkJquery);
                setupDocumentEvents();
            }
        }, 100);
    } else {
        setupDocumentEvents();
    }

    function setupDocumentEvents() {
        $(document).ready(function() {
            // Load payment options when modal opens
            $('#paymentPromptModal').on('show.bs.modal', function() {
                // Ensure proper initial state
                $('#bank-account-group').hide();
                $('#bank_account_id').removeAttr('required').val('');

                // Update labels based on entity type
                window.updateEntityLabels();

                // Handle category field based on entity type - will be properly set in handlePaymentCategories()

                // Load payment options and handle categories
                window.loadPaymentOptions();

                // Always call handlePaymentCategories to ensure proper visibility
                setTimeout(function() {
                    window.handlePaymentCategories();
                }, 50);
            });

            // Add event for when modal is fully shown
            $('#paymentPromptModal').on('shown.bs.modal', function() {
                // Check if invoice is fully paid after modal is shown
                const outstandingAmount = parseFloat($('#modal-outstanding-amount').text().replace(/,/g, '')) || 0;

                if (outstandingAmount <= 0) {

                    // Force show completion interface
                    $('#paymentForm').hide().css('display', 'none');
                    $('#fullyPaidMessage').show().css('display', 'block');
                    $('#paymentButtons').hide().css('display', 'none');
                    $('#fullyPaidButtons').show().css('display', 'block');
                } else {

                    // Force show payment interface
                    $('#paymentForm').show().css('display', 'block');
                    $('#fullyPaidMessage').hide().css('display', 'none');
                    $('#paymentButtons').show().css('display', 'block');
                    $('#fullyPaidButtons').hide().css('display', 'none');
                }

                // Run calculations after modal is shown
                window.updatePaymentCalculations();
            });

            // Update payment calculations when amount changes
            $('#payment_amount').on('input', function() {
                window.updatePaymentCalculations();
            });

            // Payment method change handler (backup)
            $(document).off('change', '#payment_method_id').on('change', '#payment_method_id', function() {
                const selectedOption = $(this).find('option:selected');
                const requiresBank = selectedOption.data('requires-bank') === 'true';
                const methodName = selectedOption.text();

                // Multiple ways to check for Bank Transfer
                const isBankTransfer = requiresBank || methodName.toLowerCase().includes('bank transfer');

                if (isBankTransfer) {
                    $('#bank-account-group').show();
                    document.getElementById('bank-account-group').style.display = 'block';
                    $('#bank_account_id').attr('required', true);
                } else {
                    $('#bank-account-group').hide();
                    document.getElementById('bank-account-group').style.display = 'none';
                    $('#bank_account_id').removeAttr('required').val('');
                }
            });

            // Full amount button
            $('#fullAmountBtn').on('click', function() {
                const maxAmount = parseFloat($('#payment_amount').attr('max'));
                $('#payment_amount').val(maxAmount.toFixed(2));
                window.updatePaymentCalculations();
            });

            // Record payment button
            $('#recordPaymentBtn').on('click', function() {
                window.recordPayment();
            });

            // Skip payment button
            $('#skipPaymentBtn').on('click', function() {
                window.skipPayment();
            });

            // Reset form when modal closes
            $('#paymentPromptModal').on('hidden.bs.modal', function() {
                $('#paymentForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Reset interface to default payment state
                $('#paymentForm').show().css('display', 'block');
                $('#fullyPaidMessage').hide().css('display', 'none');
                $('#paymentButtons').show().css('display', 'block');
                $('#fullyPaidButtons').hide().css('display', 'none');

                // Reset modal title and icon
                $('#modal-title-text').text('Record Payment');
                $('#modal-icon').removeClass('bi-check-circle-fill').addClass('bi-cash-coin');

                // Reset alert message
                $('.alert-success').removeClass('alert-success').addClass('alert-info');


            });
        });
    }
} catch (error) {
    console.error('Error setting up document ready events:', error);
}

// Define global function outside of document.ready to ensure it's always available
try {
    window.showPaymentPrompt = function(data) {
        // Store invoice data for later use
        window.currentInvoiceData = data;

        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            // Wait for jQuery to be available
            const checkJquery = setInterval(function() {
                if (typeof $ !== 'undefined') {
                    clearInterval(checkJquery);
                    showPromptModal(data);
                }
            }, 100);
        } else {
            showPromptModal(data);
        }

        function showPromptModal(data) {
            // Wait for DOM and modal to be ready
            $(document).ready(function() {
                $('#modal-entity-no').text(data.entity_no || '');
                $('#modal-party-name').text(data.party_name || '');
                $('#modal-total-amount').text((data.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
                const credit = parseFloat(data.available_credit || 0);
                if (!isNaN(credit) && credit > 0) {
                    $('#credit-row').show();
                    $('#modal-available-credit').text(credit.toLocaleString('en-US', {minimumFractionDigits: 2}));
                } else {
                    $('#credit-row').hide();
                    $('#modal-available-credit').text('0.00');
                }
                $('#modal-outstanding-amount').text((data.outstanding_amount || data.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#entity-id').val(data.entity_id);

                // Update route template based on entity type
                if (data.type === 'invoice') {
                    window.currentRouteTemplate = `/sales-invoices/${data.entity_id}/create-payment`;
                } else if (data.type === 'grn') {
                    window.currentRouteTemplate = `/grns/${data.entity_id}/create-payment`;
                }

                // Check immediately if fully paid and handle accordingly
                // Ensure outstanding amount is properly converted to number
                const outstandingAmount = parseFloat(data.outstanding_amount) || parseFloat(data.total_amount) || 0;


                if (outstandingAmount <= 0) {

                    // For fully paid invoices, set amount to 0 to prevent confusion
                    $('#payment_amount').attr('max', data.total_amount || 0).val(0);
                } else {

                    $('#payment_amount').attr('max', outstandingAmount).val(outstandingAmount);
                }

                // Update calculations and show modal
                window.updatePaymentCalculations();
                $('#paymentPromptModal').modal('show');

                // Add a small delay to ensure calculations run after modal is shown
                setTimeout(function() {
                    window.updatePaymentCalculations();

                }, 100);
            });
        }
    };
} catch (error) {
    console.error('Error defining showPaymentPrompt:', error);
}

// Ensure functions are properly exposed
try {
    // Mark that payment prompt is ready
    window.paymentPromptReady = true;


} catch (error) {
    console.error('Error marking payment prompt as ready:', error);
}
</script>
