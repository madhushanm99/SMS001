<x-layout> <x-slot name="title">Edit GRN - {{ $grn->grn_no }}</x-slot>
    <form method="POST" action="{{ route('grns.update', $grn->grn_id) }}" id="grn_form"> @csrf @method('PUT')
        <input type="hidden" id="existing_supplier_id" value="{{ $grn->supp_Cus_ID }}">
        <input type="hidden" id="existing_po_no" value="{{ $grn->po_No }}">

        <div class="row mb-3">
            <div class="col-md-4">
                <label>GRN Date</label>
                <input type="date" name="grn_date" class="form-control" value="{{ $grn->grn_date }}" required>
            </div>
            <div class="col-md-4">
                <label>PO No</label>
                <select id="po_No" name="po_No" class="form-control"></select>
            </div>
            <div class="col-md-4">
                <label>Supplier</label>
                <select id="supp_Cus_ID" name="supp_Cus_ID" class="form-control" required></select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Invoice No</label>
                <input type="text" name="invoice_no" class="form-control" value="{{ $grn->invoice_no }}">
            </div>
            <div class="col-md-4">
                <label>Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" value="{{ $grn->invoice_date }}">
            </div>
            <div class="col-md-4">
                <label>Received By</label>
                <input type="text" name="received_by" class="form-control" value="{{ $grn->received_by }}">
            </div>
        </div>

        {{-- Item Selector --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Item</label>
                <select id="item_id" class="form-control"></select>
            </div>
            <div class="col-md-2">
                <label>Qty</label>
                <input type="number" id="item_qty" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Price</label>
                <input type="text" id="item_price" class="form-control" readonly>
            </div>
            <div class="col-md-2">
                <label>Discount (%)</label>
                <input type="number" id="item_discount" class="form-control" step="0.01" min="0" max="100" value="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id="add_item_btn" class="btn btn-success w-100">Add</button>
            </div>
        </div>

        {{-- Temp Items Table --}}
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-sm">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Discount (%)</th>
                        <th>Discount Value</th>
                        <th>Line Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="grn_item_body"></tbody>
            </table>
        </div>

        <div class="text-right mb-3">
            <div class="mb-2">
                <strong>Total Discount: Rs. <span id="total_discount">0.00</span></strong>
            </div>
            <div>
                <strong>Grand Total: Rs. <span id="grand_total">0.00</span></strong>
            </div>
        </div>

        <div class="form-group mb-4">
            <label>Note</label>
            <textarea name="note" class="form-control">{{ $grn->note }}</textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary">Update GRN</button>
        </div>
    </form>
    @push('scripts')
        {{-- @include('grns.partials.js') --}}
        <script>
            let selectedItem = null;
            
            $(document).ready(function() {
                // Initialize Select2 for dropdowns
                $('#po_No').select2({
                    placeholder: 'Select PO',
                    ajax: {
                        url: '{{ route('api.purchase_orders.list') }}',
                        dataType: 'json',
                        processResults: data => ({
                            results: data
                        }),
                    }
                });
                
                $('#supp_Cus_ID').select2({
                    placeholder: 'Select Supplier',
                    ajax: {
                        url: '{{ route('api.suppliers.list') }}',
                        dataType: 'json',
                        processResults: data => ({
                            results: data
                        }),
                    }
                });
                
                $('#item_id').select2({
                    placeholder: 'Search Item',
                    ajax: {
                        url: '{{ route('api.items.search') }}',
                        dataType: 'json',
                        processResults: data => ({
                            results: data
                        }),
                    }
                }).on('select2:select', function(e) {
                    selectedItem = e.params.data;
                    $('#item_price').val(selectedItem.price);
                });

                $('#po_No').on('select2:select', function(e) {
                    let poData = e.params.data;
                    if (poData.supplier_id) {
                        $('#supp_Cus_ID').val(poData.supplier_id).trigger('change');
                        $('#supp_Cus_ID').prop('disabled', true);
                    }
                });

                $('#add_item_btn').click(async function() {
                    if (!selectedItem || !selectedItem.id) {
                        alert('Select a valid item');
                        return;
                    }
                    let qty = parseInt($('#item_qty').val());
                    if (isNaN(qty) || qty <= 0) {
                        alert('Invalid qty');
                        return;
                    }
                    let discount = parseFloat($('#item_discount').val()) || 0;
                    if (discount < 0 || discount > 100) {
                        alert('Discount must be between 0 and 100');
                        return;
                    }
                    const res = await fetch(`{{ route('grns.store_temp_item') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            item_id: selectedItem.id,
                            qty: qty,
                            discount: discount
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        renderTempItems(data.items);
                        $('#item_id').val(null).trigger('change');
                        $('#item_qty').val('');
                        $('#item_price').val('');
                        $('#item_discount').val('0');
                        selectedItem = null;
                    }
                });

                // preload PO/supplier if exists
                const poNo = $('#existing_po_no').val();
                const suppId = $('#existing_supplier_id').val();
                if (poNo) {
                    const opt = new Option(poNo, poNo, true, true);
                    $('#po_No').append(opt).trigger('change');
                    $('#supp_Cus_ID').prop('disabled', true);
                }

                if (suppId && !poNo) {
                    const opt2 = new Option(suppId, suppId, true, true);
                    $('#supp_Cus_ID').append(opt2).trigger('change');
                }

                fetchTempItems(); // load existing items
            });

            async function fetchTempItems() {
                const res = await fetch('{{ route('grns.fetch_temp_items') }}');
                const data = await res.json();
                if (data.success) renderTempItems(data.items);
            }

            function renderTempItems(items) {
                const tbody = document.getElementById('grn_item_body');
                tbody.innerHTML = '';
                let grandTotal = 0;
                let totalDiscount = 0;
                items.forEach((item, index) => {
                    const discountValue = (parseFloat(item.price) * parseFloat(item.qty) * parseFloat(item.discount || 0)) / 100;
                    totalDiscount += discountValue;
                    grandTotal += item.line_total;
                    tbody.innerHTML +=
                        ` <tr> 
                            <td>${item.item_ID}</td> 
                            <td>${item.description}</td> 
                            <td>Rs. ${parseFloat(item.price).toFixed(2)}</td> 
                            <td>${item.qty}</td> 
                            <td>${parseFloat(item.discount || 0).toFixed(2)}%</td>
                            <td>Rs. ${discountValue.toFixed(2)}</td>
                            <td>Rs. ${parseFloat(item.line_total).toFixed(2)}</td> 
                            <td><button class="btn btn-danger btn-sm" onclick="removeItem(${index})">Remove</button></td> 
                        </tr> `;
                });
                document.getElementById('total_discount').innerText = totalDiscount.toFixed(2);
                document.getElementById('grand_total').innerText = grandTotal.toFixed(2);
            }

            async function removeItem(index) {
                const res = await fetch('{{ route('grns.remove_temp_item') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        index: index
                    })
                });
                const data = await res.json();
                if (data.success) renderTempItems(data.items);
            }
        </script>
    @endpush
</x-layout>
