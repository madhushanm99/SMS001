<x-layout> <x-slot name="title">Purchase Return</x-slot>
    <form method="POST" action="{{ route('purchase_returns.store') }}" id="pr_form"> @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="grn_select">Select GRN</label>
                <select id="grn_select" class="form-control" name="grn_id" required>
                    <option value="">-- Choose GRN --</option>
                    @foreach ($grns as $grn)
                        <option value="{{ $grn->grn_id }}" data-supplier="{{ $grn->supp_Cus_ID }}">{{ $grn->grn_no }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Supplier</label>
                <input type="text" class="form-control" id="supplier_display" disabled>
                <input type="hidden" name="supp_Cus_ID" id="supp_Cus_ID">
            </div>
        </div>

        <div id="grn_item_table_wrapper" class="d-none">
            <table class="table table-bordered table-sm text-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Item ID</th>
                        <th>Name</th>
                        <th>Received</th>
                        <th>Available</th>
                        <th>Return Qty</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="return_items_body"></tbody>
            </table>
        </div>

        <div class="form-group mt-3">
            <label for="note">General Note</label>
            <textarea name="note" id="note" class="form-control"></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary">Submit Return</button>
        </div>
    </form>
    @push('scripts')
        <script>
            const grnData =
                @json($grn_items_by_grn_id);

            document.getElementById('grn_select').addEventListener('change', function() {
                const grnId = this.value;
                const supplier = this.options[this.selectedIndex]?.dataset.supplier || '';
                document.getElementById('supp_Cus_ID').value = supplier;
                document.getElementById('supplier_display').value = supplier;
                const items = grnData[grnId] || [];
                const tbody = document.getElementById('return_items_body');
                tbody.innerHTML = '';
                items.forEach((item, index) => {
                    const maxQty = item.stock_qty >= item.qty_received ? item.qty_received : item.stock_qty;
                    tbody.innerHTML +=
                        ` <tr data-index="${index}"> <td> <input type="hidden" name="items[${index}][item_ID]" value="${item.item_ID}"> ${item.item_ID} </td> <td>${item.item_Name} <input type="hidden" name="items[${index}][item_Name]" value="${item.item_Name}"> </td> <td>${item.qty_received}</td> <td>${item.stock_qty}</td> <td> <input type="number" name="items[${index}][qty_returned]" class="form-control form-control-sm return_qty" max="${maxQty}" min="1" value="${maxQty}" required> <input type="hidden" name="items[${index}][price]" value="${item.price}"> </td> <td> <input type="text" name="items[${index}][reason]" class="form-control form-control-sm"> </td> <td> <button type="button" class="btn btn-sm btn-danger remove-row">X</button> </td> </tr> `;
                });
                document.getElementById('grn_item_table_wrapper').classList.remove('d-none');
            });
            document.getElementById('return_items_body').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
            document.getElementById('return_items_body').addEventListener('input', function(e) {
                if (e.target.classList.contains('return_qty')) {
                    const max = parseInt(e.target.getAttribute('max'), 10);
                    const val = parseInt(e.target.value, 10);
                    if (val > max) {
                        alert('Return quantity cannot exceed available stock.');
                        e.target.value = max;
                    }
                }
            });
        </script>
    @endpush
</x-layout>
