<x-layout> <x-slot name="title">Customer Details - {{ $customer->custom_id }}</x-slot>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="customerTabs" role="tablist">
                <li class="nav-item"> <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
                        role="tab" aria-controls="details" aria-selected="true">Details</a> </li>
                <li class="nav-item"> <a class="nav-link" id="history-tab" data-toggle="tab" href="#history"
                        role="tab" aria-controls="history" aria-selected="false">Visit History</a> </li>
            </ul>
        </div>
        <div class="card-body tab-content" id="customerTabContent">
            {{-- Tab 1: Customer Details --}}
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <h5>{{ $customer->name }}</h5>
                <p><strong>Customer ID:</strong> {{ $customer->custom_id }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $customer->email ?? '-' }}</p>
                <p><strong>NIC:</strong> {{ $customer->nic ?? '-' }}</p>
                <p><strong>Group:</strong> {{ $customer->group_name ?? 'All Groups' }}</p>
                <p><strong>Balance Credit:</strong> Rs. {{ number_format($customer->balance_credit, 2) }}</p>
                <p><strong>Address:</strong> {{ $customer->address ?? '-' }}</p>
                <p><strong>Last Visit:</strong>
                    {{ $customer->last_visit ? $customer->last_visit->format('Y-m-d H:i') : 'N/A' }}</p>
                <p>
                    <strong>Status:</strong>
                    @if ($customer->status)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </p>
            </div>

            {{-- Tab 2: Visit History Placeholder --}}
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <p class="text-muted">Visit history will appear here in the future.</p>
            </div>
        </div>
    </div>
    <div class="mt-3"> <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">← Back</a> </div>
    @push('scripts')
        <script>
            
            $(function() {
                $('#customerTabs a').on('click', function(e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
            });
        </script>
    @endpush
</x-layout>
