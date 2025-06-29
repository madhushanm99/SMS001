<x-layout>
    <x-slot name="title">Edit Purchase Order</x-slot>

    <div class="container mx-auto mt-6">
        <h1 class="text-2xl font-bold mb-4">Edit Purchase Order - {{ $purchaseOrder->po_No }}</h1>

        <form action="{{ route('purchase_orders.update', $purchaseOrder->po_Auto_ID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="po_date" class="block font-medium">PO Date</label>
                    <input type="date" name="po_date" id="po_date" class="w-full border border-gray-300 rounded p-2"
                        value="{{ $purchaseOrder->po_date }}" required>
                </div>

                <div>
                    <label for="supp_Cus_ID" class="block font-medium">Supplier</label>
                    <select name="supp_Cus_ID" id="supp_Cus_ID" class="w-full border border-gray-300 rounded p-2"
                        required>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->Supp_CustomID }}"
                                {{ $supplier->Supp_CustomID == $purchaseOrder->supp_Cus_ID ? 'selected' : '' }}>
                                {{ $supplier->Supp_Name ?? $supplier->Company_Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="mb-2">
                    <label for="item_id" class="block text-sm font-medium">Item ID</label>
                    <select id="item_id" class="item-select w-full border px-2 py-1" style="width: 100%"></select>
                </div>
                <div>
                    <label class="block font-medium">Description</label>
                    <input type="text" id="item_description"
                        class="w-full border border-gray-200 rounded p-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-medium">Price</label>
                    <input type="text" id="item_price" class="w-full border border-gray-200 rounded p-2 bg-gray-100"
                        readonly>
                </div>
                <div>
                    <label class="block font-medium">Qty</label>
                    <input type="number" id="item_qty" class="w-full border border-gray-300 rounded p-2">
                </div>
            </div>

            <div class="mb-4">
                <button type="button" id="add_item_btn"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Add Item
                </button>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="w-full table-auto border border-gray-200" id="po_items_table">
                    <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">Item ID</th>
                            <th class="px-4 py-2 border">Description</th>
                            <th class="px-4 py-2 border">Price</th>
                            <th class="px-4 py-2 border">Qty</th>
                            <th class="px-4 py-2 border">Line Total</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="po_item_body">
                        <!-- JS will render this -->
                    </tbody>
                </table>
                <div class="mt-4 text-right">
                    <strong>Grand Total: Rs. <span id="grand_total">0.00</span></strong>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="note" class="block font-medium">Note</label>
                    <input type="text" name="note" id="note"
                        class="w-full border border-gray-300 rounded p-2" value="{{ $purchaseOrder->note }}">
                </div>
                <div>
                    <label for="Reff_No" class="block font-medium">Reference No</label>
                    <input type="text" name="Reff_No" id="Reff_No"
                        class="w-full border border-gray-300 rounded p-2" value="{{ $purchaseOrder->Reff_No }}">
                </div>
                <input type="hidden" name="emp_Name" value="{{ $purchaseOrder->emp_Name }}">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update PO
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <!-- Include jQuery and jQuery UI -->
        <script>
            let selectedItem = null;

            // Initialize autocomplete
            $(document).ready(function() {
                // Initialize Select2 with AJAX
                $('#item_id').select2({
                    placeholder: 'Search for a product',
                    ajax: {
                        url: '{{ route('items.search') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });

                // When an item is selected from Select2
                $('#item_id').on('select2:select', function(e) {
                    selectedItem = e.params.data;
                    console.log("Selected Item:", selectedItem);
                    $('#item_price').val(selectedItem.price);
                    $('#item_description').val(selectedItem.desc);
                });

                // Optional: Support for manual typing an exact item ID
                $('#item_id_input').on('blur', async function() {
                    const itemId = this.value.trim();
                    if (!itemId) return;

                    try {
                        const response = await fetch(`/purchase-orders/get-item-details/${itemId}`);
                        if (!response.ok) throw new Error();

                        const item = await response.json();
                        selectedItem = {
                            item_ID: item.item_ID,
                            price: item.sales_Price,
                            desc: item.product_Type
                        };

                        $('#item_price').val(selectedItem.price);
                        $('#item_description').val(selectedItem.desc);
                    } catch (error) {
                        selectedItem = null;
                        $('#item_price').val('');
                        $('#item_description').val('');
                    }
                });

                // Add item to the list
                $('#add_item_btn').on('click', async function() {
                    if (!selectedItem || !selectedItem.id) {
                        alert('Please select a valid item ID');
                        return;
                    }

                    const qty = parseInt($('#item_qty').val());
                    if (isNaN(qty) || qty <= 0) {
                        alert('Enter a valid quantity');
                        return;
                    }

                    const response = await fetch(`{{ route('purchase_orders.store_temp_item') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            item_id: selectedItem.id,
                            qty: qty
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        renderTempItems(data.items);

                        // Reset form fields
                        $('#item_id').val(null).trigger('change');
                        $('#item_id_input').val('');
                        $('#item_price').val('');
                        $('#item_description').val('');
                        $('#item_qty').val('');
                        selectedItem = null;
                    }
                });
            });

            const fetchAndRenderItems = async () => {
                const res = await fetch(`{{ route('purchase_orders.fetch_temp_items') }}`);
                const data = await res.json();
                if (data.success) {
                    renderTempItems(data.items);
                }
            }

            document.getElementById('add_item_btn').addEventListener('click', async function() {
                if (!selectedItem) {
                    alert('Please select a valid item ID');
                    return;
                }

                const qty = parseInt(document.getElementById('item_qty').value);
                if (isNaN(qty) || qty <= 0) {
                    alert('Enter a valid quantity');
                    return;
                }

                const response = await fetch(`{{ route('purchase_orders.store_temp_item') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        item_id: selectedItem.item_ID,
                        qty: qty
                    })
                });

                const data = await response.json();
                if (data.success) {
                    renderTempItems(data.items);
                    document.getElementById('item_id_input').value = '';
                    document.getElementById('item_price').value = '';
                    document.getElementById('item_description').value = '';
                    document.getElementById('item_qty').value = '';
                    selectedItem = null;
                }
            });

            function renderTempItems(items) {
                const tbody = document.getElementById('po_item_body');
                tbody.innerHTML = '';
                let grandTotal = 0;
                items.forEach((item, index) => {
                    const lineTotal = item.line_total;
                    grandTotal += lineTotal;
                    const row = `
                        <tr class="text-sm">
                            <td class="px-4 py-2 border">${item.item_ID}</td>
                            <td class="px-4 py-2 border">${item.description}</td>
                            <td class="px-4 py-2 border">Rs. ${item.price}</td>
                            <td class="px-4 py-2 border">${item.qty}</td>
                            <td class="px-4 py-2 border">Rs. ${item.line_total.toFixed(2)}</td>
                            <td class="px-4 py-2 border">
                                <button type="button" class="text-red-600 hover:underline" onclick="removeItem(${index})">Remove</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
            }

            async function removeItem(index) {
                const response = await fetch(`{{ route('purchase_orders.remove_temp_item') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        index: index
                    })
                });

                const data = await response.json();
                if (data.success) {
                    renderTempItems(data.items);
                }
            }

            // Initial fetch on load
            fetchAndRenderItems();
        </script>
    @endpush
</x-layout>
