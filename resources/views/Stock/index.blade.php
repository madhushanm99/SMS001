<x-layout> <x-slot name="title">Stock Overview</x-slot>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4>Stock Levels</h4> <input type="text" id="search_box" class="form-control w-25"
            placeholder="Search by ID or Name" value="{{ $search ?? '' }}">
    </div>
    <div id="stock_table_wrapper"> @include('stock.table', ['items' => $items]) </div>
    @push('scripts')
        <script>
            const searchInput = document.getElementById('search_box');
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('stock.index') }}?search=${encodeURIComponent(this.value)}`)
                        .then(res => res.text())
                        .then(html => {
                            const dom = new DOMParser().parseFromString(html, 'text/html');
                            const newTable = dom.getElementById('stock_table_wrapper').innerHTML;
                            document.getElementById('stock_table_wrapper').innerHTML = newTable;
                        });
                }, 300);
            });
        </script>
    @endpush
</x-layout>
