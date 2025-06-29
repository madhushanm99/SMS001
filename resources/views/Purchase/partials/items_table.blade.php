<table class="table table-bordered compact-table">
    <thead>
        <tr>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Product Type</th>
            <th>Category</th>
            <th >Selling Price</th>

        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
        <tr class="clickable-row" data-href="{{ route('products.show', $item->item_ID_Auto) }}">
                {{-- <td><a href="{{ route('suppliers.show', $supplier->Supp_ID) }}">{{ $supplier->Supp_CustomID }}</a></td> --}}
                <td>{{ $item->item_ID }}</td>
                <td> {{ \Illuminate\Support\Str::limit($item->item_Name, 30, '...') }}</td>
                <td>{{ $item->product_Type }}</td>
                <td>{{ $item->catagory_Name }}</td>
                <td >{{ $item->sales_Price }}</td>
            </tr>


        @empty
            <tr>
                <td colspan="7">No suppliers found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    document.querySelectorAll('.clickable-row').forEach(function(row) {
    row.addEventListener('click', function() {
        window.location = this.dataset.href;
    });
});
</script>

<div>
    {{ $items->links() }}
</div>
