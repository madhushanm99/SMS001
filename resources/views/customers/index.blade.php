<x-layout> <x-slot name="title">Customer List</x-slot>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Customers</h4> <a href="{{ route('customers.create') }}" class="btn btn-primary">+ New Customer</a>
    </div>
    <form id="filter_form" class="row g-2 mb-3">
        <div class="col-md-3"> <input type="text" name="search" id="search" class="form-control"
                placeholder="Search by name, NIC, phone, email"> </div>
        <div class="col-md-2"> <input type="date" name="from" id="from" class="form-control"
                placeholder="From"> </div>
        <div class="col-md-2"> <input type="date" name="to" id="to" class="form-control"
                placeholder="To"> </div>
        <div class="col-md-2"> <button type="submit" class="btn btn-secondary w-100">Filter</button> </div>
    </form>
    <div id="customer_table_wrapper"> @include('customers.table', ['customers' => $customers]) </div>
    @push('scripts')
        <script>
            const form = document.getElementById('filter_form');
            let typingTimer;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchResults();
            });

            document.getElementById('search').addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => fetchResults(), 300);
            });

            function fetchResults(page = 1) {
                const params = new URLSearchParams(new FormData(form));
                fetch(`{{ route('customers.index') }}?${params.toString()}`)
                    .then(res => res.text())
                    .then(html => {
                        const dom = new DOMParser().parseFromString(html, 'text/html');
                        const newTable = dom.getElementById('customer_table_wrapper').innerHTML;
                        document.getElementById('customer_table_wrapper').innerHTML = newTable;
                    });
            }

            document.addEventListener('click', function(e) {
                if (e.target.matches('.pagination a')) {
                    e.preventDefault();
                    const url = new URL(e.target.href);
                    const page = url.searchParams.get('page');
                    fetchResults(page);
                }
            });
        </script>
    @endpush
</x-layout>
