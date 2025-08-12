<x-layout>
    <x-slot name="title">Edit Service Invoice</x-slot>

    <div class="pagetitle">
        <h1>Edit Service Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('service_invoices.index') }}">Service Invoices</a></li>
                <li class="breadcrumb-item active">Edit {{ $serviceInvoice->invoice_no }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Edit Service Invoice: {{ $serviceInvoice->invoice_no }}</h5>
                        <div>
                            <span class="badge bg-warning">{{ ucfirst($serviceInvoice->status) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('service_invoices.update', $serviceInvoice) }}" id="editServiceInvoiceForm">
                            @csrf
                            @method('PUT')

                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="editServiceInvoiceTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="edit-customer-tab" data-bs-toggle="tab" data-bs-target="#edit-customer" type="button" role="tab">
                                        <i class="bi bi-person"></i> Customer & Vehicle
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-jobs-tab" data-bs-toggle="tab" data-bs-target="#edit-jobs" type="button" role="tab">
                                        <i class="bi bi-tools"></i> Job Types
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-spares-tab" data-bs-toggle="tab" data-bs-target="#edit-spares" type="button" role="tab">
                                        <i class="bi bi-gear"></i> Spare Parts
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="editServiceInvoiceTabContent">
                                <!-- Customer & Vehicle Tab -->
                                <div class="tab-pane fade show active" id="edit-customer" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_customer_search" class="form-label">Customer <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{ $serviceInvoice->customer ? $serviceInvoice->customer->name . ' (' . $serviceInvoice->customer->phone . ')' : 'N/A' }}" readonly>
                                                <input type="hidden" name="customer_id" value="{{ $serviceInvoice->customer_id }}">
                                                <div class="form-text text-muted">Customer cannot be changed for existing invoices</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_vehicle_search" class="form-label">Vehicle Number</label>
                                                <input type="text" class="form-control" value="{{ $serviceInvoice->vehicle_no ?? 'N/A' }}" readonly>
                                                <input type="hidden" name="vehicle_no" value="{{ $serviceInvoice->vehicle_no }}">
                                                <div class="form-text text-muted">Vehicle cannot be changed for existing invoices</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_mileage" class="form-label">Mileage (km)</label>
                                                <input type="number" class="form-control" id="edit_mileage" name="mileage"
                                                       value="{{ old('mileage', $serviceInvoice->mileage) }}" min="0">
                                                @error('mileage')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_notes" class="form-label">Notes</label>
                                                <textarea class="form-control" id="edit_notes" name="notes" rows="3">{{ old('notes', $serviceInvoice->notes) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Types Tab -->
                                <div class="tab-pane fade" id="edit-jobs" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6>Add Job Type</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="edit_job_search" placeholder="Search job types...">
                                                    <input type="hidden" id="edit_selected_job_id">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control" id="edit_job_qty" placeholder="Qty" min="1" value="1">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="edit_job_price" placeholder="Price" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-primary" id="edit_add_job_btn">
                                                        <i class="bi bi-plus"></i> Add Job
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6>Selected Job Types</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="edit_job_items_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Job Type</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Price</th>
                                                            <th>Line Total</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Items will be loaded via AJAX -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-warning">
                                                            <th colspan="3">Job Total</th>
                                                            <th id="edit_job_total">Rs. 0.00</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Spare Parts Tab -->
                                <div class="tab-pane fade" id="edit-spares" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6>Add Spare Part</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="edit_item_search" placeholder="Search spare parts...">
                                                    <input type="hidden" id="edit_selected_item_id">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control" id="edit_item_qty" placeholder="Qty" min="1" value="1">
                                                    <div class="form-text" id="edit_item_stock"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="edit_item_price" placeholder="Price" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-primary" id="edit_add_item_btn">
                                                        <i class="bi bi-plus"></i> Add Item
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6>Selected Spare Parts</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="edit_spare_items_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Item Name</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Price</th>
                                                            <th>Line Total</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Items will be loaded via AJAX -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-info">
                                                            <th colspan="3">Parts Total</th>
                                                            <th id="edit_parts_total">Rs. 0.00</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Invoice Summary -->
                                    <div class="row mt-4">
                                        <div class="col-md-6 offset-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Invoice Summary</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td>Job Total:</td>
                                                            <td class="text-end" id="edit_summary_job_total">Rs. 0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Parts Total:</td>
                                                            <td class="text-end" id="edit_summary_parts_total">Rs. 0.00</td>
                                                        </tr>
                                                        <tr class="table-success">
                                                            <th>Grand Total:</th>
                                                            <th class="text-end" id="edit_grand_total">Rs. 0.00</th>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('service_invoices.index') }}" class="btn btn-secondary">
                                                    <i class="bi bi-arrow-left"></i> Cancel
                                                </a>
                                                <div>
                                                    <button type="submit" class="btn btn-primary me-2">
                                                        <i class="bi bi-save"></i> Update Invoice
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
    $(document).ready(function() {
        // Customer and vehicle fields are now readonly in edit mode
        // No need for Select2 initialization for these fields

        // Initialize job search autocomplete
        if ($.fn.autocomplete) {
            $('#edit_job_search').autocomplete({
                source: function(request, response) {
                    $.get('{{ route("service_invoices.job_search") }}', {
                        term: request.term
                    }, function(data) {
                        response(data);
                    });
                },
                select: function(event, ui) {
                    $('#edit_selected_job_id').val(ui.item.id);
                    $('#edit_job_price').val(ui.item.price);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.text + " - Rs. " + item.price + "</div>")
                    .appendTo(ul);
            };
        } else {
            console.warn('jQuery UI autocomplete not available, using simple input fields');
        }

        // Initialize item search autocomplete
        if ($.fn.autocomplete) {
            $('#edit_item_search').autocomplete({
                source: function(request, response) {
                    $.get('{{ route("service_invoices.item_search") }}', {
                        term: request.term
                    }, function(data) {
                        response(data);
                    });
                },
                select: function(event, ui) {
                    $('#edit_selected_item_id').val(ui.item.id);
                    $('#edit_item_price').val(ui.item.price);
                    const stockQty = ui.item.stock_qty || 0;
                    $('#edit_item_qty').attr('max', stockQty);
                    $('#edit_item_stock').text(stockQty > 0 ? `Available: ${stockQty}` : 'Out of stock');
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.text + " - Rs. " + item.price + "</div>")
                    .appendTo(ul);
            };
        } else {
            console.warn('jQuery UI autocomplete not available, using simple input fields');
        }

        // Add job item
        $('#edit_add_job_btn').click(function() {
            const jobId = $('#edit_selected_job_id').val();
            const description = $('#edit_job_search').val();
            const qty = $('#edit_job_qty').val();
            const price = $('#edit_job_price').val();

            if (!jobId || !description || !qty || !price) {
                alert('Please fill all job fields');
                return;
            }

            $.post('{{ route("service_invoices.add_job_item") }}', {
                _token: '{{ csrf_token() }}',
                item_id: jobId,
                description: description,
                qty: qty,
                price: price,
                edit_mode: 1
            }, function(response) {
                if (response.success) {
                    loadJobItems();
                    clearJobForm();
                    updateTotals();
                }
            });
        });

        // Add spare item
        $('#edit_add_item_btn').click(function() {
            const itemId = $('#edit_selected_item_id').val();
            const description = $('#edit_item_search').val();
            const qty = $('#edit_item_qty').val();
            const price = $('#edit_item_price').val();

            if (!itemId || !description || !qty || !price) {
                alert('Please fill all item fields');
                return;
            }

            const available = parseInt($('#edit_item_qty').attr('max') || '0');
            if (available <= 0) {
                alert('This item is out of stock and cannot be added.');
                return;
            }
            if (parseInt(qty) > available) {
                alert(`Insufficient stock. Available: ${available}`);
                return;
            }

            $.post('{{ route("service_invoices.add_spare_item") }}', {
                _token: '{{ csrf_token() }}',
                item_id: itemId,
                description: description,
                qty: qty,
                price: price,
                edit_mode: 1
            }, function(response) {
                if (response.success) {
                    loadSpareItems();
                    clearItemForm();
                    updateTotals();
                } else {
                    alert(response.message || 'Failed to add item');
                }
            });
        });

        // Load job items
        function loadJobItems() {
            console.log('Loading job items...');
            $.ajax({
                url: '{{ route("service_invoices.get_job_items") }}',
                method: 'GET',
                data: {edit_mode: 1},
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Job items response:', response);
                    const tbody = $('#edit_job_items_table tbody');
                    tbody.empty();

                    if (response.items && response.items.length > 0) {
                        response.items.forEach(function(item, index) {
                            tbody.append(`
                                <tr>
                                    <td>${item.description}</td>
                                    <td>${item.qty}</td>
                                    <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                                    <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJobItem(${index})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No job items found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading job items:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('#edit_job_items_table tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading job items</td></tr>');
                }
            });
        }

        // Load spare items
        function loadSpareItems() {
            console.log('Loading spare items...');
            $.ajax({
                url: '{{ route("service_invoices.get_spare_items") }}',
                method: 'GET',
                data: {edit_mode: 1},
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Spare items response:', response);
                    const tbody = $('#edit_spare_items_table tbody');
                    tbody.empty();

                    if (response.items && response.items.length > 0) {
                        response.items.forEach(function(item, index) {
                            tbody.append(`
                                <tr>
                                    <td>${item.description}</td>
                                    <td>${item.qty}</td>
                                    <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                                    <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSpareItem(${index})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No spare items found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading spare items:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('#edit_spare_items_table tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading spare items</td></tr>');
                }
            });
        }

        // Remove job item
        window.removeJobItem = function(index) {
            $.post('{{ route("service_invoices.remove_job_item") }}', {
                _token: '{{ csrf_token() }}',
                index: index,
                edit_mode: 1
            }, function(response) {
                if (response.success) {
                    loadJobItems();
                    updateTotals();
                }
            });
        };

        // Remove spare item
        window.removeSpareItem = function(index) {
            $.post('{{ route("service_invoices.remove_spare_item") }}', {
                _token: '{{ csrf_token() }}',
                index: index,
                edit_mode: 1
            }, function(response) {
                if (response.success) {
                    loadSpareItems();
                    updateTotals();
                }
            });
        };

        // Update totals
        function updateTotals() {
            Promise.all([
                $.get('{{ route("service_invoices.get_job_items") }}', {edit_mode: 1}),
                $.get('{{ route("service_invoices.get_spare_items") }}', {edit_mode: 1})
            ]).then(function([jobResponse, spareResponse]) {
                const jobTotal = jobResponse.items.reduce((sum, item) => sum + parseFloat(item.line_total), 0);
                const spareTotal = spareResponse.items.reduce((sum, item) => sum + parseFloat(item.line_total), 0);
                const grandTotal = jobTotal + spareTotal;

                $('#edit_job_total').text('Rs. ' + jobTotal.toFixed(2));
                $('#edit_parts_total').text('Rs. ' + spareTotal.toFixed(2));
                $('#edit_summary_job_total').text('Rs. ' + jobTotal.toFixed(2));
                $('#edit_summary_parts_total').text('Rs. ' + spareTotal.toFixed(2));
                $('#edit_grand_total').text('Rs. ' + grandTotal.toFixed(2));
            });
        }

        // Clear forms
        function clearJobForm() {
            $('#edit_job_search').val('');
            $('#edit_selected_job_id').val('');
            $('#edit_job_qty').val('1');
            $('#edit_job_price').val('');
        }

        function clearItemForm() {
            $('#edit_item_search').val('');
            $('#edit_selected_item_id').val('');
            $('#edit_item_qty').val('1');
            $('#edit_item_price').val('');
            $('#edit_item_stock').text('');
        }

        // Debug: Check session data directly
        console.log('Session data check:');
        console.log('Job items in session:', @json(session('edit_service_invoice_job_items', [])));
        console.log('Spare items in session:', @json(session('edit_service_invoice_spare_items', [])));

        // Load initial data
        loadJobItems();
        loadSpareItems();
        updateTotals();

        // Fallback: If AJAX fails, load from session data directly
        setTimeout(function() {
            const jobItems = @json(session('edit_service_invoice_job_items', []));
            const spareItems = @json(session('edit_service_invoice_spare_items', []));

            if (jobItems.length > 0) {
                console.log('Loading job items from session fallback:', jobItems);
                renderJobItemsFromSession(jobItems);
            }

            if (spareItems.length > 0) {
                console.log('Loading spare items from session fallback:', spareItems);
                renderSpareItemsFromSession(spareItems);
            }
        }, 2000);

        function renderJobItemsFromSession(items) {
            const tbody = $('#edit_job_items_table tbody');
            tbody.empty();

            items.forEach(function(item, index) {
                tbody.append(`
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.qty}</td>
                        <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJobItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function renderSpareItemsFromSession(items) {
            const tbody = $('#edit_spare_items_table tbody');
            tbody.empty();

            items.forEach(function(item, index) {
                tbody.append(`
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.qty}</td>
                        <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeSpareItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
    });
    </script>
    @endpush

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    @endpush
</x-layout>
