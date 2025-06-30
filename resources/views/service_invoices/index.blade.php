<x-layout>
    <x-slot name="title">Service Invoices</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Service Invoices</h4>
        <a href="{{ route('service_invoices.create') }}" class="btn btn-primary">+ New Service Invoice</a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" id="search_input" class="form-control" placeholder="Search by invoice no, customer name, phone, or vehicle no..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select id="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="hold" {{ request('status') == 'hold' ? 'selected' : '' }}>Hold</option>
                        <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="clear_filters" class="btn btn-outline-secondary w-100">Clear Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div id="invoices_table_wrapper">
        @include('service_invoices.table', ['invoices' => $invoices])
    </div>

    @push('scripts')
    <script>
        let searchTimeout;

        function performSearch() {
            const search = $('#search_input').val();
            const status = $('#status_filter').val();
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (status) params.append('status', status);
            
            const url = `{{ route('service_invoices.index') }}?${params.toString()}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                $('#invoices_table_wrapper').html(html);
                // Update URL without page reload
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }

        $('#search_input').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        $('#status_filter').on('change', performSearch);

        $('#clear_filters').on('click', function() {
            $('#search_input').val('');
            $('#status_filter').val('');
            performSearch();
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            
            fetch($(this).attr('href'), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                $('#invoices_table_wrapper').html(html);
                window.history.pushState({}, '', $(this).attr('href'));
            });
        });
    </script>
    @endpush
</x-layout> 