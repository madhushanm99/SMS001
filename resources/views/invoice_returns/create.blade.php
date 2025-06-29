<x-layout title="Create Invoice Return">
    <div class="pagetitle">
        <h1>Create Invoice Return</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice_returns.index') }}">Invoice Returns</a></li>
                <li class="breadcrumb-item active">Create Return</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Invoice Details Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Original Invoice Details</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
                                <p><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
                                <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Total Amount:</strong> Rs. {{ number_format($invoice->grand_total, 2) }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                                </p>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('invoice_returns.select') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left"></i> Change Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Form Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Return Items</h5>

                        <!-- Original Invoice Items -->
                        <div class="mb-4">
                            <h6>Available Items for Return:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty Sold</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Return Qty</th>
                                            <th>Reason</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                        <tr>
                                            <td>{{ $item->item_name }} <br><small class="text-muted">{{ $item->item_id }}</small></td>
                                            <td>{{ $item->qty }}</td>
                                            <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->discount }}%</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm return-qty" 
                                                       data-item-id="{{ $item->id }}" min="1" max="{{ $item->qty }}" 
                                                       placeholder="0" style="width: 80px;">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm return-reason" 
                                                       data-item-id="{{ $item->id }}" placeholder="Optional reason" 
                                                       style="width: 150px;">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm add-return-item" 
                                                        data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-plus"></i> Add
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Return Items List -->
                        <div class="mb-4">
                            <h6>Items to Return:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="return-items-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty Returning</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Line Total</th>
                                            <th>Reason</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-items-body">
                                        <!-- Return items will be added here -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-warning">
                                            <td colspan="4" class="text-end"><strong>Total Return Amount:</strong></td>
                                            <td><strong>Rs. <span id="return-total">0.00</span></strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Return Details Form -->
                        <form id="return-form">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="reason" class="form-label">Return Reason *</label>
                                    <select class="form-select" id="reason" name="reason" required>
                                        <option value="">Select reason for return</option>
                                        <option value="Defective Item">Defective Item</option>
                                        <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                                        <option value="Customer Changed Mind">Customer Changed Mind</option>
                                        <option value="Quality Issues">Quality Issues</option>
                                        <option value="Damaged During Delivery">Damaged During Delivery</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes about the return..."></textarea>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" id="process-return-btn" class="btn btn-success" disabled>
                                    <i class="bi bi-check-circle"></i> Process Return
                                </button>
                                <a href="{{ route('invoice_returns.select') }}" class="btn btn-secondary ms-2">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Add return item
            $('.add-return-item').click(function() {
                const itemId = $(this).data('item-id');
                const qtyInput = $(`.return-qty[data-item-id="${itemId}"]`);
                const reasonInput = $(`.return-reason[data-item-id="${itemId}"]`);
                
                const qty = parseInt(qtyInput.val());
                const reason = reasonInput.val();

                if (!qty || qty <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: 'Please enter a valid return quantity'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route("invoice_returns.add_item") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        invoice_item_id: itemId,
                        qty_returned: qty,
                        return_reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            updateReturnItemsTable(response.items);
                            updateReturnTotal(response.total);
                            
                            // Reset form inputs
                            qtyInput.val('');
                            reasonInput.val('');
                            
                            // Enable process button if items exist
                            $('#process-return-btn').prop('disabled', response.items.length === 0);
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

            // Remove return item
            $(document).on('click', '.remove-return-item', function() {
                const itemId = $(this).data('item-id');
                
                $.ajax({
                    url: '{{ route("invoice_returns.remove_item") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        invoice_item_id: itemId
                    },
                    success: function(response) {
                        if (response.success) {
                            updateReturnItemsTable(response.items);
                            updateReturnTotal(response.total);
                            $('#process-return-btn').prop('disabled', response.items.length === 0);
                        }
                    }
                });
            });

            // Process return
            $('#process-return-btn').click(function() {
                const reason = $('#reason').val();

                if (!reason) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Return Reason Required',
                        text: 'Please select a reason for the return'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Process Return?',
                    text: 'This will process the return and update stock levels. This action cannot be undone.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Process Return'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitReturn();
                    }
                });
            });

            function submitReturn() {
                $.ajax({
                    url: '{{ route("invoice_returns.store") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        invoice_id: $('input[name="invoice_id"]').val(),
                        reason: $('#reason').val(),
                        notes: $('#notes').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Return Processed',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 2000);
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

            function updateReturnItemsTable(items) {
                const tbody = $('#return-items-body');
                tbody.empty();

                items.forEach(function(item) {
                    const row = `
                        <tr>
                            <td>${item.item_name}<br><small class="text-muted">${item.item_id}</small></td>
                            <td>${item.qty_returned} / ${item.original_qty}</td>
                            <td>Rs. ${numberWithCommas(item.unit_price)}</td>
                            <td>${item.discount}%</td>
                            <td>Rs. ${numberWithCommas(item.line_total)}</td>
                            <td><small>${item.return_reason}</small></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-return-item" 
                                        data-item-id="${item.invoice_item_id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function updateReturnTotal(total) {
                $('#return-total').text(numberWithCommas(total));
            }

            function numberWithCommas(x) {
                return parseFloat(x).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Load existing session items if any
            $.get('{{ route("invoice_returns.session_items") }}', function(response) {
                if (response.success && response.items.length > 0) {
                    updateReturnItemsTable(response.items);
                    updateReturnTotal(response.total);
                    $('#process-return-btn').prop('disabled', false);
                }
            });
        });
    </script>
    @endpush
</x-layout> 