<x-layout>
    <x-slot name="title">Create Service Invoice</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Create Service Invoice</h4>
        <a href="{{ route('service_invoices.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <form id="service-invoice-form" method="POST" action="{{ route('service_invoices.store') }}">
        @csrf

        <!-- Tab Navigation -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer-pane" type="button" role="tab">
                            <i class="bi bi-person-check me-2"></i>
                            1. Customer & Vehicle
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs-pane" type="button" role="tab">
                            <i class="bi bi-tools me-2"></i>
                            2. Job Types
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="parts-tab" data-bs-toggle="tab" data-bs-target="#parts-pane" type="button" role="tab">
                            <i class="bi bi-gear me-2"></i>
                            3. Spare Parts
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab 1: Customer & Vehicle Selection -->
                    <div class="tab-pane fade show active" id="customer-pane" role="tabpanel">
                        <h5 class="mb-4">Customer & Vehicle Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_select" class="form-label">Customer *</label>
                                    <select name="customer_id" id="customer_select" class="form-control" required>
                                        <option value="">Search and select customer...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_select" class="form-label">Vehicle <span class="text-muted" style="font-weight: normal;">(only approved)</span></label>
                                    <select name="vehicle_no" id="vehicle_select" class="form-control" disabled>
                                        <option value="">Select customer first...</option>
                                    </select>
                                    <div class="form-text">Select a customer first to see available vehicles</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mileage" class="form-label">Mileage</label>
                                    <input type="number" name="mileage" id="mileage" class="form-control" placeholder="Enter current mileage" min="0">
                                    <div class="form-text">Current vehicle mileage</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any additional notes for this service invoice..."></textarea>
                        </div>
                    </div>

                    <!-- Tab 2: Job Types -->
                    <div class="tab-pane fade" id="jobs-pane" role="tabpanel">
                        <h5 class="mb-4">Job Types & Services</h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="job_selector" class="form-label">Search Job Type</label>
                                <select id="job_selector" class="form-control">
                                    <option value="">Search for job types...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="job_qty" class="form-label">Quantity</label>
                                <input type="number" id="job_qty" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <label for="job_price" class="form-label">Price</label>
                                <input type="number" id="job_price" class="form-control" step="0.01" min="0" readonly>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" id="add_job_btn">
                                    Add Job
                                </button>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Added Job Types</h6>
                            </div>
                            <div class="card-body">
                                <div id="job_items_container">
                                    <div class="text-muted text-center py-3">No job types added yet</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Spare Parts -->
                    <div class="tab-pane fade" id="parts-pane" role="tabpanel">
                        <h5 class="mb-4">Spare Parts & Items</h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="spare_selector" class="form-label">Search Spare Part</label>
                                <select id="spare_selector" class="form-control">
                                    <option value="">Search for spare parts...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="spare_qty" class="form-label">Quantity</label>
                                <input type="number" id="spare_qty" class="form-control" min="1" value="1">
                                <div class="form-text" id="spare_stock" ></div>
                            </div>
                            <div class="col-md-2">
                                <label for="spare_price" class="form-label">Price</label>
                                <input type="number" id="spare_price" class="form-control" step="0.01" min="0" readonly>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" id="add_spare_btn">
                                    Add Part
                                </button>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Added Spare Parts</h6>
                            </div>
                            <div class="card-body">
                                <div id="spare_items_container">
                                    <div class="text-muted text-center py-3">No spare parts added yet</div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">Invoice Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-muted">Job Types Total:</div>
                                        <div class="fs-5">Rs. <span id="jobs_total">0.00</span></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted">Spare Parts Total:</div>
                                        <div class="fs-5">Rs. <span id="parts_total">0.00</span></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted">Grand Total:</div>
                                        <div class="fs-4 fw-bold text-primary">Rs. <span id="grand_total">0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning me-2">
                                Save as Hold
                            </button>
                            <button type="submit" class="btn btn-success" name="finalize" value="1">
                                Finalize Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        let selectedCustomerId = '';
        let jobItems = [];
        let spareItems = [];

        $(document).ready(function() {
            initializeCustomerSearch();
            initializeJobSearch();
            initializeSpareSearch();

            // Initialize vehicle select as disabled
            clearVehicles();
        });

        function initializeCustomerSearch() {
            $('#customer_select').select2({
                placeholder: 'Search customer...',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('service_invoices.customer_search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                selectedCustomerId = e.params.data.id;
                console.log('Customer selected:', selectedCustomerId);
                loadVehicles(selectedCustomerId);
            }).on('select2:clear', function(e) {
                selectedCustomerId = '';
                clearVehicles();
            });
        }

        function loadVehicles(customerId) {
            console.log('Loading vehicles for customer:', customerId);

            try {
                // Properly destroy existing Select2 instance if it exists
                if ($('#vehicle_select').hasClass('select2-hidden-accessible')) {
                    $('#vehicle_select').select2('destroy');
                }
            } catch (e) {
                console.warn('Error destroying Select2:', e);
            }

            // Clear and reset the select element
            $('#vehicle_select').empty().append('<option value="">Select vehicle...</option>');

            if (!customerId) {
                $('#vehicle_select').prop('disabled', true);
                return;
            }

            // Initialize new Select2 instance with customer filtering
            $('#vehicle_select').select2({
                placeholder: 'Select vehicle...',
                allowClear: true,
                ajax: {
                    url: '{{ route('service_invoices.vehicle_search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            customer_id: customerId
                        };
                    },
                    processResults: function(data) {
                        console.log('Vehicle search results:', data);
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $('#vehicle_select').prop('disabled', false);
        }

        function clearVehicles() {
            try {
                // Properly destroy existing Select2 instance if it exists
                if ($('#vehicle_select').hasClass('select2-hidden-accessible')) {
                    $('#vehicle_select').select2('destroy');
                }
            } catch (e) {
                console.warn('Error destroying Select2:', e);
            }
            $('#vehicle_select').empty().append('<option value="">Select customer first...</option>');
            $('#vehicle_select').prop('disabled', true);
        }

        function initializeJobSearch() {
            $('#job_selector').select2({
                placeholder: 'Search job types...',
                ajax: {
                    url: '{{ route('service_invoices.job_search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ term: params.term }),
                    processResults: data => ({ results: data }),
                }
            }).on('select2:select', function(e) {
                $('#job_price').val(e.params.data.price);
            });
        }

        function initializeSpareSearch() {
            $('#spare_selector').select2({
                placeholder: 'Search spare parts...',
                ajax: {
                    url: '{{ route('service_invoices.item_search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ term: params.term }),
                    processResults: data => ({ results: data }),
                }
            }).on('select2:select', function(e) {
                $('#spare_price').val(e.params.data.price);
                const stockQty = e.params.data.stock_qty ?? 0;
                $('#spare_qty').attr('max', stockQty);
                $('#spare_stock').text(stockQty > 0 ? `Available: ${stockQty}` : 'Out of stock');
            });
        }

        $('#add_job_btn').on('click', function() {
            const selectedJob = $('#job_selector').select2('data')[0];
            const qty = parseInt($('#job_qty').val());
            const price = parseFloat($('#job_price').val());

            if (!selectedJob || !qty || qty < 1 || !price || price < 0) {
                alert('Please select a job type, enter valid quantity and price.');
                return;
            }

            fetch('{{ route('service_invoices.add_job_item') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_id: selectedJob.id,
                    description: selectedJob.text,
                    qty: qty,
                    price: price,
                })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    renderJobItems(data.items);
                    $('#job_selector').val(null).trigger('change');
                    $('#job_qty').val(1);
                    $('#job_price').val('');
                    updateTotals();
                }
            });
        });

        $('#add_spare_btn').on('click', function() {
            const selectedSpare = $('#spare_selector').select2('data')[0];
            const qty = parseInt($('#spare_qty').val());
            const price = parseFloat($('#spare_price').val());

            if (!selectedSpare || !qty || qty < 1 || !price || price < 0) {
                alert('Please select a spare part, enter valid quantity and price.');
                return;
            }

            const available = parseInt(selectedSpare.stock_qty || 0);
            if (available <= 0) {
                alert('This item is out of stock and cannot be added.');
                return;
            }
            if (qty > available) {
                alert(`Insufficient stock. Available: ${available}`);
                return;
            }

            fetch('{{ route('service_invoices.add_spare_item') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_id: selectedSpare.id,
                    description: selectedSpare.text,
                    qty: qty,
                    price: price,
                })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    renderSpareItems(data.items);
                    $('#spare_selector').val(null).trigger('change');
                    $('#spare_qty').val(1);
                    $('#spare_price').val('');
                    $('#spare_stock').text('');
                    updateTotals();
                } else {
                    alert(data.message || 'Failed to add item');
                }
            });
        });

        function renderJobItems(items) {
            jobItems = items;
            if (items.length === 0) {
                $('#job_items_container').html('<div class="text-muted text-center py-3">No job types added yet</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Job Type</th><th>Qty</th><th>Price</th><th>Total</th><th>Action</th></tr></thead><tbody>';
            items.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.qty}</td>
                        <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeJobItem(${index})"><i class="bi bi-trash"></i></button></td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            $('#job_items_container').html(html);
        }

        function renderSpareItems(items) {
            spareItems = items;
            if (items.length === 0) {
                $('#spare_items_container').html('<div class="text-muted text-center py-3">No spare parts added yet</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Spare Part</th><th>Qty</th><th>Price</th><th>Total</th><th>Action</th></tr></thead><tbody>';
            items.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.qty}</td>
                        <td>Rs. ${parseFloat(item.price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSpareItem(${index})"><i class="bi bi-trash"></i></button></td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            $('#spare_items_container').html(html);
        }

        function removeJobItem(index) {
            fetch('{{ route('service_invoices.remove_job_item') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ index: index })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    renderJobItems(data.items);
                    updateTotals();
                }
            });
        }

        function removeSpareItem(index) {
            fetch('{{ route('service_invoices.remove_spare_item') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ index: index })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    renderSpareItems(data.items);
                    updateTotals();
                }
            });
        }

        function updateTotals() {
            const jobsTotal = jobItems.reduce((sum, item) => sum + parseFloat(item.line_total), 0);
            const partsTotal = spareItems.reduce((sum, item) => sum + parseFloat(item.line_total), 0);
            const grandTotal = jobsTotal + partsTotal;

            $('#jobs_total').text(jobsTotal.toFixed(2));
            $('#parts_total').text(partsTotal.toFixed(2));
            $('#grand_total').text(grandTotal.toFixed(2));
        }
    </script>
    @endpush
</x-layout>
