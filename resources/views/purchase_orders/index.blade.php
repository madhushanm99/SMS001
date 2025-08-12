<x-layout>
    <x-slot name="title">Purchase Orders</x-slot>

    <div class="container mx-auto mt-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Purchase Orders</h1>
            <a href="{{ route('purchase_orders.create') }}"
                class="btn btn-primary rounded px-3 py-2">
                + New Purchase Order
            </a>
        </div>

        {{-- @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif --}}

        <form method="GET" action="{{ route('purchase_orders.index') }}" class="mb-4 flex gap-4">
            <select name="supplier" class="border px-2 py-1">
                <option value="">All Suppliers</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->Supp_CustomID }}"
                        {{ request('supplier') == $supplier->Supp_CustomID ? 'selected' : '' }}>
                        {{ $supplier->Supp_Name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border px-2 py-1">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border px-2 py-1">

            <button class="btn-secondary text-white px-3 py-1">Filter</button>
        </form>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">PO No</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Supplier</th>
                        <th class="px-4 py-2 border">Grand Total</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="px-4 py-2 border">{{ $po->po_No }}</td>
                            <td class="px-4 py-2 border">{{ $po->po_date }}</td>
                            <td class="px-4 py-2 border">{{ $po->supplier_name ?? $po->supp_Cus_ID }}</td>
                            <td class="px-4 py-2 border">Rs. {{ number_format($po->grand_Total, 2) }}</td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ getStatusBadge($po->orderStatus) }}">
                                    {{ ucfirst($po->orderStatus) }}
                                </span>

                            </td>
                            <td class="px-4 py-2 border text-center space-x-2">
                                <a href="{{ route('purchase_orders.edit', $po->po_Auto_ID) }}"
                                    class="text-blue-600 hover:underline">Edit</a>

                                <form action="{{ route('purchase_orders.destroy', $po->po_Auto_ID) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                                <a href="{{ route('purchase_orders.pdf', $po->po_Auto_ID) }}"
                                    class="btn btn-sm btn-primary" target="_blank">PDF</a>
                                <form action="{{ route('purchase_orders.status', $po->po_Auto_ID) }}" method="POST">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="border text-sm">
                                        @foreach (['draft', 'pending', 'approved', 'received', 'cancelled'] as $status)
                                            <option value="{{ $status }}"
                                                {{ $po->orderStatus == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                No purchase orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $purchaseOrders->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this purchase order?')) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush

</x-layout>
