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
                                    <label for="vehicle_select" class="form-label">Vehicle</label>
                                    <select name="vehicle_no" id="vehicle_select" class="form-control">
                                        <option value="">Select customer first...</option>
                                    </select>
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
        let vehicleSelect2 = null;

        $(document).ready(function() {
            initializeCustomerSearch();
            initializeJobSearch();
            initializeSpareSearch();
        });

        function initializeCustomerSearch() {
            $('#customer_select').select2({
                placeholder: 'Search customer...',
                ajax: {
                    url: '{{ route('service_invoices.search_customers') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ term: params.term }),
                    processResults: data => ({ results: data }),
                }
            }).on('select2:select', function(e) {
                selectedCustomerId = e.params.data.id;
                loadVehicles(selectedCustomerId);
            });
        }

        function loadVehicles(customerId) {
            // Destroy existing Select2 instance if it exists
            if (vehicleSelect2) {
                vehicleSelect2.destroy();
            }
            
            // Clear the select element
            $('#vehicle_select').empty().append('<option value="">Select vehicle...</option>');
            
            // Initialize new Select2 instance
            vehicleSelect2 = $('#vehicle_select').select2({
                placeholder: 'Select vehicle...',
                ajax: {
                    url: '{{ route('service_invoices.search_vehicles') }}',
                    data: params => ({
                        q: params.term,
                        customer_id: customerId
                    }),
                    processResults: data => ({ results: data }),
                }
            });
        }

        function initializeJobSearch() {
            $('#job_selector').select2({
                placeholder: 'Search job types...',
                ajax: {
                    url: '{{ route('service_invoices.search_jobs') }}',
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
                    url: '{{ route('service_invoices.search_items') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ term: params.term }),
                    processResults: data => ({ results: data }),
                }
            }).on('select2:select', function(e) {
                $('#spare_price').val(e.params.data.price);
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
                    updateTotals();
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