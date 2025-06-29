<x-layout title="Edit Sales Invoice">
    @if(!isset($invoice))
        <div class="alert alert-danger">
            <h4><i class="bi bi-exclamation-triangle"></i> Error</h4>
            <p>Invoice data could not be loaded. This might be due to:</p>
            <ul>
                <li>The invoice was not found</li>
                <li>You don't have permission to edit this invoice</li>
                <li>A system error occurred</li>
            </ul>
            <a href="{{ route('sales_invoices.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
            </a>
        </div>
    @else
    <div class="pagetitle">
        <h1>Edit Sales Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales_invoices.index') }}">Sales Invoices</a></li>
                <li class="breadcrumb-item active">Edit #{{ $invoice->invoice_no }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Invoice #{{ $invoice->invoice_no }}</h5>
                        <p class="text-muted">Status: <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span></p>
                        
                        @if($invoice->status === 'finalized')
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>Warning: Editing Finalized Invoice</strong><br>
                                        <small>You are editing a finalized invoice. Any changes will automatically adjust stock quantities. Original stock will be restored first, then new quantities will be deducted.</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form id="invoice-form">
                            @csrf
                            @method('PUT')
                            
                            <!-- Customer Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select id="customer_id" name="customer_id" class="form-select" required>
                                        <option value="{{ $invoice->customer_id }}" selected>
                                            {{ $invoice->customer->name }} - {{ $invoice->customer->phone }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ $invoice->notes }}</textarea>
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
                                                    <td colspan="6" class="text-center text-muted">Loading items...</td>
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
                                                    <td class="text-end">Rs. <span id="grand-total">{{ number_format($invoice->grand_total, 2) }}</span></td>
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
                                    <button type="button" class="btn btn-success" id="update-btn">
                                        <i class="bi bi-check-circle"></i> 
                                        {{ $invoice->status === 'finalized' ? 'Update Finalized Invoice' : 'Update Invoice' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        let selectedItem = null;

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

            // Load existing items from session
            loadSessionItems();

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

            // Update invoice
            $('#update-btn').click(function() {
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

                $.ajax({
                    url: '{{ route("sales_invoices.update", $invoice->id) }}',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        customer_id: customerId,
                        notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Invoice Updated',
                                text: 'Invoice has been updated successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1500);
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
        });

        function loadSessionItems() {
            // Load items from session via the API endpoint
            $.ajax({
                url: '{{ route("sales_invoices.get_session_items") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.items) {
                        updateItemsTable(response.items);
                        updateGrandTotal(response.total);
                    } else {
                        updateItemsTable([]);
                        updateGrandTotal(0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading session items:', error);
                    // If session items can't be loaded, show empty table
                    updateItemsTable([]);
                    updateGrandTotal(0);
                }
            });
        }

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
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item from the invoice?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("sales_invoices.remove_temp_item") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            item_id: itemId,
                            invoice_id: {{ $invoice->id }} // Pass invoice ID for stock handling
                        },
                        success: function(response) {
                            if (response.success) {
                                updateItemsTable(response.items);
                                updateGrandTotal(response.total);
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Item Removed',
                                    text: 'Item has been removed from the invoice',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to remove item'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to remove item. Please try again.'
                            });
                        }
                    });
                }
            });
        }
    </script>
    @endpush
    @endif
</x-layout> 