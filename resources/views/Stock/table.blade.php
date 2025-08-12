<table class="table table-sm table-bordered text-sm">
    <thead class="thead-light">
        <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Cost Value</th>
            <th>Selling Price</th>
            <th>Re-Order Level</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            <tr @if ($item->qty <= ($item->reorder_level ?? 0)) style="background-color: #fff3cd;" @endif>
                <td>{{ $item->item_ID }}</td>
                <td>{{ $item->item_Name }}</td>
                <td>{{ $item->qty }}</td>
                <td>Rs. {{ number_format($item->cost_value, 2) }}</td>
                <td>Rs. {{ number_format($item->sales_Price, 2) }}</td>
                <td>{{ $item->reorder_level }}</td>
        </tr> @empty <tr>
                <td colspan="6" class="text-center text-muted">No items found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3"> {{ $items->links() }} </div>
