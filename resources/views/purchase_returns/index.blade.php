<x-layout> <x-slot name="title">Purchase Returns</x-slot>
    <div class="d-flex justify-content-between mb-3">
        <h4>Purchase Returns</h4> <a href="{{ route('purchase_returns.create') }}" class="btn btn-primary">+ New
            Return</a>
    </div>
    <form method="GET" class="mb-3 row g-2">
        <div class="col-md-4"> <select name="supplier" class="form-control">
                <option value="">-- Filter by Supplier --</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->Supp_CustomID }}"
                        {{ request('supplier') == $s->Supp_CustomID ? 'selected' : '' }}> {{ $s->Supp_Name }} </option>
                    @endforeach
            </select> </div>
        <div class="col-md-4"> <select name="grn" class="form-control">
                <option value="">-- Filter by GRN --</option>
                @foreach ($grns as $g)
                    <option value="{{ $g->grn_no }}" {{ request('grn') == $g->grn_no ? 'selected' : '' }}>
                        {{ $g->grn_no }} </option>
                @endforeach
            </select> </div>
        <div class="col-md-4"> <button class="btn btn-secondary">Apply</button> </div>
    </form>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-sm text-sm">
        <thead class="thead-light">
            <tr>
                <th>Return No</th>
                <th>GRN No</th>
                <th>Supplier</th>
                <th>Returned By</th>
                <th>Date</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $pr)
                <tr>
                    <td>{{ $pr->return_no }}</td>
                    <td>{{ $pr->grn_no }}</td>
                    <td>{{ $pr->supp_Cus_ID }}</td>
                    <td>{{ $pr->returned_by }}</td>
                    <td>{{ $pr->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if ($pr->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Deleted</span>
                            @endif
                    </td>
                    <td class="text-center"> <a href="{{ route('purchase_returns.pdf', $pr->id) }}"
                            class="btn btn-sm btn-outline-dark" target="_blank">PDF</a>
                        @if ($pr->status)
                            <form action="{{ route('purchase_returns.destroy', $pr->id) }}" method="POST"
                                class="d-inline-block"> @csrf @method('DELETE') <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this return?')">Delete</button> </form>
                        @endif
                    </td>
            </tr> @empty <tr>
                    <td colspan="7" class="text-center text-muted">No returns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-layout>
