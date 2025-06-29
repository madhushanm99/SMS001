<table class="table table-bordered compact-table">
    <thead>
        <tr>
            <th>Supp ID</th>
            <th>Name</th>
            <th>Company Name</th>
            <th>Phone</th>
            <th class="d-none d-md-table-cell">Fax</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($suppliers as $supplier)
        <tr class="clickable-row" data-href="{{ route('suppliers.show', $supplier->Supp_ID) }}">
                {{-- <td><a href="{{ route('suppliers.show', $supplier->Supp_ID) }}">{{ $supplier->Supp_CustomID }}</a></td> --}}
                <td>{{ $supplier->Supp_CustomID }}</td>
                <td> {{ $supplier->Supp_Name }} </td>
                <td>{{ $supplier->Company_Name }}</td>
                <td>{{ $supplier->Phone }}</td>
                <td class="d-none d-md-table-cell">{{ $supplier->Fax }}</td>
                <td>{{ $supplier->Email }}</td>
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
    {{ $suppliers->links() }}
</div>
