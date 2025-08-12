<x-layout title="Create Sales Invoice">
    <div class="pagetitle">
        <h1>Create Sales Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales_invoices.index') }}">Sales Invoices</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Invoice Details</h5>

                        <form id="invoice-form">
                            @csrf
                            <!-- Customer Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select id="customer_id" name="customer_id" class="form-select" required>
                                        <option value="">Select Customer</option>
                                    </select>
                                    <div class="invalid-feedback" id="customer-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2"
                                              placeholder="Optional notes..."></textarea>
                                </div>
                            </div>

                            <!-- Item Selection -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Add Items</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="item_search" class="form-label">Search Item</label>
                                            <select id="item_search" class="form-select">
                                                <option value="">Search for items...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="qty" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="qty" min="1" value="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="discount" class="form-label">Discount (%)</label>
                                            <input type="number" class="form-control" id="discount" min="0" max="100" value="0" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="stock_qty" class="form-label">Stock Available</label>
                                            <input type="text" class="form-control" id="stock_qty" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-primary d-block w-100" id="add-item">
                                                <i class="bi bi-plus"></i> Add Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Invoice Items</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Unit Price</th>
                                                    <th>Qty</th>
                                                    <th>Discount (%)</th>
                                                    <th>Line Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items-tbody">
                                                <tr id="no-items-row">
                                                    <td colspan="6" class="text-center text-muted">No items added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Grand Total:</th>
                                                    <td class="text-end">Rs. <span id="grand-total">0.00</span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('sales_invoices.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                                <div>
                                    <button type="button" class="btn btn-warning me-2" id="hold-btn">
                                        <i class="bi bi-pause-circle"></i> Hold Invoice
                                    </button>
                                    <button type="button" class="btn btn-success" id="finalize-btn">
                                        <i class="bi bi-check-circle"></i> Create Invoice
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Payment Prompt Modal -->
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
        let selectedItem = null;
        let items = [];

        $(document).ready(function() {
            // Initialize customer search
            $('#customer_id').select2({
                placeholder: 'Search for customer...',
                ajax: {
                    url: '{{ route("sales_invoices.search_customers") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            // Initialize item search
            $('#item_search').select2({
                placeholder: 'Search for items...',
                ajax: {
                    url: '{{ route("sales_invoices.search_items") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            // Handle item selection
            $('#item_search').on('select2:select', function (e) {
                selectedItem = e.params.data;
                $('#stock_qty').val(selectedItem.stock_qty);
                $('#qty').attr('max', selectedItem.stock_qty);
            });

            // Add item to list
            $('#add-item').click(function() {
                if (!selectedItem) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Item Selected',
                        text: 'Please select an item first'
                    });
                    return;
                }

                const qty = parseInt($('#qty').val());
                const discount = parseFloat($('#discount').val()) || 0;

                if (qty <= 0 || qty > selectedItem.stock_qty) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Quantity',
                        text: `Invalid quantity or insufficient stock. Available: ${selectedItem.stock_qty}`
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route("sales_invoices.add_temp_item") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        item_id: selectedItem.id,
                        qty: qty,
                        discount: discount
                    },
                    success: function(response) {
                        if (response.success) {
                            updateItemsTable(response.items);
                            updateGrandTotal(response.total);

                            // Reset form
                            $('#item_search').val(null).trigger('change');
                            $('#qty').val(1);
                            $('#discount').val(0);
                            $('#stock_qty').val('');
                            selectedItem = null;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    }
                });
            });

            // Hold invoice
            $('#hold-btn').click(function() {
                submitInvoice('hold');
            });

            // Finalize invoice
            $('#finalize-btn').click(function() {
                submitInvoice('finalize');
            });
        });

        function updateItemsTable(itemsData) {
            const tbody = $('#items-tbody');
            tbody.empty();

            if (itemsData.length === 0) {
                tbody.append('<tr id="no-items-row"><td colspan="6" class="text-center text-muted">No items added</td></tr>');
                return;
            }

            itemsData.forEach(function(item) {
                const row = `
                    <tr>
                        <td>${item.item_name}<br><small class="text-muted">${item.item_id}</small></td>
                        <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>${item.qty}</td>
                        <td>${item.discount}%</td>
                        <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="removeItem('${item.item_id}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function updateGrandTotal(total) {
            $('#grand-total').text(parseFloat(total).toFixed(2));
        }

        function removeItem(itemId) {
            $.ajax({
                url: '{{ route("sales_invoices.remove_temp_item") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    item_id: itemId
                },
                success: function(response) {
                    if (response.success) {
                        updateItemsTable(response.items);
                        updateGrandTotal(response.total);
                    }
                }
            });
        }

        function submitInvoice(action) {
            const customerId = $('#customer_id').val();
            const notes = $('#notes').val();

            if (!customerId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Customer Required',
                    text: 'Please select a customer'
                });
                return;
            }

            const url = action === 'hold' ? '{{ route("sales_invoices.hold") }}' : '{{ route("sales_invoices.finalize") }}';

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    customer_id: customerId,
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        // If any low stock alerts, show a red warning first
                        if (response.low_stock_alerts && response.low_stock_alerts.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Reorder Level Reached',
                                html: '<div class="text-start">' + response.low_stock_alerts.map(msg => `<div class="text-danger">• ${msg}</div>`).join('') + '</div>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        }

                        if (action === 'finalize' && response.prompt_payment) {
                            // Show payment prompt modal
                            if (typeof window.showPaymentPrompt === 'function') {
                                window.showPaymentPrompt({
                                    type: 'invoice',
                                    entity_id: response.payment_data.invoice_id,
                                    entity_no: response.payment_data.invoice_no,
                                    party_name: response.payment_data.customer_name,
                                    total_amount: response.payment_data.grand_total,
                                    outstanding_amount: response.payment_data.outstanding_amount
                                });
                            } else {
                                // Fallback if payment prompt is not available
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Invoice Created Successfully!',
                                    text: 'What would you like to do next?',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonText: 'View PDF',
                                    denyButtonText: 'Email Invoice',
                                    cancelButtonText: 'Go to Invoice List',
                                    confirmButtonColor: '#3085d6',
                                    denyButtonColor: '#28a745',
                                    cancelButtonColor: '#6c757d'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.open(response.pdf_url, '_blank');
                                        window.location.href = response.redirect_url;
                                    } else if (result.isDenied) {
                                        emailInvoice(response.invoice_id);
                                    } else {
                                        window.location.href = response.redirect_url;
                                    }
                                });
                            }
                        } else if (action === 'finalize') {
                            // Fallback if payment prompt is not enabled
                            Swal.fire({
                                icon: 'success',
                                title: 'Invoice Created Successfully!',
                                text: 'What would you like to do next?',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: 'View PDF',
                                denyButtonText: 'Email Invoice',
                                cancelButtonText: 'Go to Invoice List',
                                confirmButtonColor: '#3085d6',
                                denyButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open(response.pdf_url, '_blank');
                                    window.location.href = response.redirect_url;
                                } else if (result.isDenied) {
                                    emailInvoice(response.invoice_id);
                                } else {
                                    window.location.href = response.redirect_url;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Invoice Saved',
                                text: 'Invoice has been saved as hold',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1500);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                }
            });
        }

        function emailInvoice(invoiceId) {
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
                        }).then(() => {
                            window.location.href = '{{ route("sales_invoices.index") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Email Failed',
                            text: response.message
                        }).then(() => {
                            window.location.href = '{{ route("sales_invoices.index") }}';
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to send email. Please try again.'
                    }).then(() => {
                        window.location.href = '{{ route("sales_invoices.index") }}';
                    });
                }
            });
        }
    </script>
@endpush
</x-layout>
