<x-layout> <x-slot name="title">Quotations</x-slot>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4>All Quotations</h4> <a href="{{ route('quotations.create') }}" class="btn btn-primary">+ New Quotation</a>
    </div>
    <div class="row mb-3">
        <div class="col-md-4"> <input type="text" id="search_box" class="form-control"
                placeholder="Search by name, phone, NIC, or vehicle"> </div>

        <div class="col-md-2"> <input type="date" id="filter_from" class="form-control" /> </div>
        <div class="col-md-2"> <input type="date" id="filter_to" class="form-control" /> </div>
        <div class="col-md-2"> <button class="btn btn-outline-primary w-100" id="apply_filter_btn">Filter</button>
        </div>
    </div>
    <div id="quotation_table_wrapper"> @include('quotations.table', ['quotations' => $quotations]) </div>
    @push('scripts')
        <script>
            document.getElementById('search_box').addEventListener('input', function() {
                const searchTerm = this.value;
                const url = "{{ route('quotations.index') }}";
                fetch(url + "?search=" + encodeURIComponent(searchTerm)).then(response => response.text()).then(
                    html => {
                        const wrapper = document.getElementById('quotation_table_wrapper');
                        const parsed = new DOMParser().parseFromString(html, 'text/html');
                        wrapper.innerHTML = parsed.getElementById('quotation_table_wrapper').innerHTML;
                    });
            });
            document.getElementById('apply_filter_btn').addEventListener('click', function() {
                const from = document.getElementById('filter_from').value;
                const to = document.getElementById('filter_to').value;
                const url = "{{ route('quotations.index') }}";
                fetch(url + '?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to)).then(res => res
                .text()).then(html => {
                    const wrapper = document.getElementById('quotation_table_wrapper');
                    const parsed = new DOMParser().parseFromString(html, 'text/html');
                    wrapper.innerHTML = parsed.getElementById('quotation_table_wrapper').innerHTML;
                });
            });
        </script>
    @endpush
</x-layout>
