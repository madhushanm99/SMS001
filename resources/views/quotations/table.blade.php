<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>Quotation No</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($quotations as $q)
            <tr>
                <td>{{ $q->quotation_no }}</td>
                <td>{{ $q->customer_name }}</td>
                <td>{{ $q->vehicle_no ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($q->quotation_date)->format('Y-m-d') }}</td>
                <td>Rs. {{ number_format($q->grand_total, 2) }}</td>
                <td> <span class="badge {{ $q->status ? 'badge-success' : 'badge-secondary' }}">
                        {{ $q->status ? 'Active' : 'Inactive' }} </span> </td>
                <td> <a href="{{ route('quotations.pdf', $q->id) }}" target="_blank" class="btn btn-sm btn-info">View</a> </td>
                <td> 
                    <a href="{{ route('quotations.edit', $q->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="{{ route('quotations.destroy', $q->id) }}" style="display: inline-block;" 
                          onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
        </tr> @empty <tr>
                <td colspan="7" class="text-center">No quotations found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{ $quotations->links() }}
