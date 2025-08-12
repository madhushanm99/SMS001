<x-layout> <x-slot name="title">Create GRN</x-slot>
    <form method="POST" action="{{ route('grns.store') }}" id="grn_form"> @csrf
        {{-- Top Row: GRN Date + PO No --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="grn_date">GRN Date</label>
                <input type="date" name="grn_date" id="grn_date" class="form-control" value="{{ date('Y-m-d') }}"
                    required>
            </div>

            <div>
                <label for="po_No">PO Number (Optional)</label>
                <select id="po_No" name="po_No" class="form-control"></select>
            </div>

            <div>
                <label for="supp_Cus_ID">Supplier</label>
                <select id="supp_Cus_ID" name="supp_Cus_ID" class="form-control" required></select>
            </div>
        </div>

        {{-- Invoice & Received --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="invoice_no">Supplier Invoice No</label>
                <input type="text" name="invoice_no" id="invoice_no" class="form-control">
            </div>
            <div>
                <label for="invoice_date">Invoice Date</label>
                <input type="date" name="invoice_date" id="invoice_date" class="form-control">
            </div>
            <div>
                <label for="received_by">Received By</label>
                <input type="text" name="received_by" id="received_by" class="form-control"
                    value="{{ auth()->user()->name }}">
            </div>
        </div>

        {{-- Item Selection --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <label for="item_id">Item</label>
                <select id="item_id" class="form-control"></select>
            </div>
            <div>
                <label>Qty</label>
                <input type="number" id="item_qty" class="form-control">
            </div>
            <div>
                <label>Price</label>
                <input type="text" id="item_price" class="form-control" readonly>
            </div>
            <div>
                <label>Discount (%)</label>
                <input type="number" id="item_discount" class="form-control" step="0.01" min="0" max="100" value="0">
            </div>
            <div class="pt-6">
                <button type="button" class="btn btn-success w-full" id="add_item_btn">Add Item</button>
            </div>
        </div>

        {{-- Temp Items Table --}}
        <div class="table-responsive">
            <table class="table table-bordered text-sm">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Discount (%)</th>
                        <th>Discount Value</th>
                        <th>Line Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="grn_item_body"></tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="text-right mb-4">
            <div class="mb-2">
                <strong>Total Discount: Rs. <span id="total_discount">0.00</span></strong>
            </div>
            <div>
                <strong>Grand Total: Rs. <span id="grand_total">0.00</span></strong>
            </div>
        </div>

        {{-- Note --}}
        <div class="mb-4">
            <label for="note">Note</label>
            <textarea name="note" id="note" class="form-control"></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary">Save GRN</button>
        </div>
    </form>
    @push('scripts')
        {{-- @include('grns.partials.js') --}}

        <script>
            let selectedItem = null;
            $(document).ready(function() {
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
                $('#po_No').on('select2:select', async function(e) {
                    let poData = e.params.data;
                    if (poData.supplier_id) {
                        $('#supp_Cus_ID').val(poData.supplier_id).trigger('change');
                        $('#supp_Cus_ID').prop('disabled', true);
                    }
                    // Import PO items into GRN temp items
                    if (poData && poData.id) {
                        try {
                            // Need auto ID; API currently returns po_No as id. Fetch PO by number to get auto ID.
                            const resp = await fetch(`{{ url('/api/purchase-orders/by-number') }}?po_no=${encodeURIComponent(poData.id)}`);
                            const poInfo = await resp.json();
                            const poAutoId = poInfo?.po_Auto_ID;
                            if (poAutoId) {
                                const res = await fetch(`{{ route('grns.import_from_po') }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ po_auto_id: poAutoId })
                                });
                                const data = await res.json();
                                if (data.success) {
                                    renderTempItems(data.items);
                                }
                            }
                        } catch (err) {
                            console.error('Failed to import PO items', err);
                        }
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
                fetchTempItems();
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
                    const discountValue = (parseFloat(item.price) * parseFloat(item.qty) * parseFloat(item.discount)) / 100;
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
