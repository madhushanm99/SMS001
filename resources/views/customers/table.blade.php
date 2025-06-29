<table class="table table-bordered table-sm text-sm">
    <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>NIC</th>
            <th>Last Visit</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($customers as $c)
            <tr>
                <td>{{ $c->custom_id }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->phone }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->nic }}</td>
                <td>{{ $c->last_visit ? $c->last_visit->format('Y-m-d') : '-' }}</td>
                <td>
                    @if ($c->status)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </td>
                <td> <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-outline-secondary">View</a> <a
                        href="{{ route('customers.edit', $c->id) }}" class="btn btn-sm btn-info">Edit</a> </td>
        </tr> @empty <tr>
                <td colspan="8" class="text-center text-muted">No customers found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-2"> {{ $customers->links() }} </div>
