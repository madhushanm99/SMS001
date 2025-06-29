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
            <div class="col-md-5">
                <label>Item</label>
                <select id="item_id" class="form-control"></select>
            </div>
            <div class="col-md-2">
                <label>Qty</label>
                <input type="number" id="item_qty" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Price</label>
                <input type="text" id="item_price" class="form-control" readonly>
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
                        <th>Line Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="grn_item_body"></tbody>
            </table>
        </div>

        <div class="text-right mb-3">
            <strong>Grand Total: Rs. <span id="grand_total">0.00</span></strong>
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
            // preload PO/supplier if exists
            $(function() {
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
        </script>
    @endpush
</x-layout>
