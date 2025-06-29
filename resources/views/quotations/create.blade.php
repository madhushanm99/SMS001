<x-layout> <x-slot name="title">Create Quotation</x-slot>
    <form id="quotation-form"> @csrf
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_custom_id" id="customer_select" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Vehicle</label>
            <select name="vehicle_no" id="vehicle_select" class="form-control"></select>
        </div>

        <div class="mb-3">
            <label>Item Type</label>
            <div>
                <label class="mr-2"><input type="radio" name="item_type" value="spare" checked> Spare Part</label>
                <label><input type="radio" name="item_type" value="job"> Job Type</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Item</label>
                <select id="item_selector" class="form-control" style="width: 100%"></select>
            </div>
            <div class="col-md-3">
                <label>Qty</label>
                <input type="number" id="qty_input" class="form-control" min="1" value="1">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="add_item_btn">Add</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm text-sm">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Line Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="quotation_items_body"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right font-weight-bold">Total</td>
                        <td colspan="2" id="grand_total">Rs. 0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-success">Create Quotation</button>
        </div>
    </form>
    @push('scripts')
        <script>
            let selectedType = 'spare';
            let selectedCustomerId = '';
            let selectedVehicle = '';
            $('input[name="item_type"]').on('change', function() {
                selectedType = $(this).val();
                $('#item_selector').val(null).trigger('change');
                setupItemSearch();
            });

            $('#customer_select').select2({
                placeholder: 'Search customer',
                ajax: {
                    url: '{{ route('quotations.customer_search') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                }
            }).on('select2:select', function(e) {
                selectedCustomerId = e.params.data.id;
                $('#vehicle_select').val(null).trigger('change');
                loadVehicles(selectedCustomerId);
            });

            $('#vehicle_select').select2({
                placeholder: 'Select vehicle',
                ajax: {
                    url: '{{ route('quotations.vehicle_search') }}',
                    data: params => ({
                        q: params.term,
                        customer_id: selectedCustomerId
                    }),
                    processResults: data => ({
                        results: data
                    }),
                }
            });

            function setupItemSearch() {
                $('#item_selector').select2({
                    placeholder: 'Search item',
                    ajax: {
                        url: selectedType === 'spare' ? '{{ route('quotations.item_search') }}' :
                            '{{ route('quotations.job_search') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: data
                        }),
                    }
                });
            }

            setupItemSearch();

            $('#add_item_btn').on('click', function() {
                const selectedItem = $('#item_selector').select2('data')[0];
                const qty = parseInt($('#qty_input').val());
                if (!selectedItem || !qty || qty < 1) return alert('Select item and quantity.');

                fetch(`{{ route('quotations.add_temp_item') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        item_id: selectedItem.id,
                        description: selectedItem.text,
                        qty: qty,
                        price: selectedItem.price,
                        type: selectedType,
                    })
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        renderItems(data.items);
                    }
                });
            });

            function renderItems(items) {
                const body = document.getElementById('quotation_items_body');
                body.innerHTML = '';
                let total = 0;

                items.forEach((item, i) => {
                    total += item.line_total;
                    const row = `
        <tr>
          <td>${item.type}</td>
          <td>${item.description}</td>
          <td>${item.qty}</td>
          <td>Rs. ${item.price}</td>
          <td>Rs. ${item.line_total.toFixed(2)}</td>
          <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${i})">Remove</button></td>
        </tr>`;
                    body.insertAdjacentHTML('beforeend', row);
                });

                document.getElementById('grand_total').innerText = 'Rs. ' + total.toFixed(2);
            }

            function removeItem(index) {
                fetch(`{{ route('quotations.remove_temp_item') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        index: index
                    })
                }).then(res => res.json()).then(data => {
                    if (data.success) renderItems(data.items);
                });
            }

            $('#quotation-form').on('submit', function(e) {
                e.preventDefault();
                const customer_id = $('#customer_select').val();
                const vehicle_no = $('#vehicle_select').val();

                fetch(`{{ route('quotations.store') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            customer_custom_id: customer_id,
                            vehicle_no: vehicle_no
                        })
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Quotation Created!',
                                text: 'Quotation #' + data.quotation_no + ' created successfully.',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Print Quotation',
                                cancelButtonText: 'No, Go Back',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open(data.pdf_url, '_blank');
                                }
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to create quotation.', 'error');
                        }
                    });
            });
        </script>
    @endpush
</x-layout>
