<x-layout> <x-slot name="title">Registered Vehicles</x-slot>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4>Vehicles</h4> <a href="{{ route('vehicles.create') }}" class="btn btn-primary">+ New Vehicle</a>
    </div>
    <div class="row g-2 mb-3">
        <div class="col-md-4"> <input type="text" id="search_box" class="form-control"
                placeholder="Search by Reg No, Name, NIC, Phone"> </div>
    </div>
    <div id="vehicle_table_wrapper"> @include('vehicles.table', ['vehicles' => $vehicles]) </div>
    @push('scripts')
        <script>
            const searchInput = document.getElementById('search_box');
            searchInput.addEventListener('input', function() {
                const search = encodeURIComponent(this.value);
                fetch(`{{ route('vehicles.index') }}?search=${search}`).then(res => res.text()).then(html => {
                    const dom = new DOMParser().parseFromString(html, 'text/html');
                    const newTable = dom.getElementById('vehicle_table_wrapper').innerHTML;
                    document.getElementById('vehicle_table_wrapper').innerHTML = newTable;
                });
            });
        </script>
    @endpush
</x-layout>
