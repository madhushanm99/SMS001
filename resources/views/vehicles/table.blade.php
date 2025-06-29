<table class="table table-bordered table-sm text-sm">
    <thead>
        <tr>
            <th>Reg No</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>NIC</th>
            <th>Brand</th>
            <th>Model</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($vehicles as $v)
            <tr>
                <td>{{ $v->vehicle_no }}</td>
                <td>{{ $v->customer->name }}</td>
                <td>{{ $v->customer->phone }}</td>
                <td>{{ $v->customer->nic }}</td>
                <td>{{ $v->brand->name ?? '-' }}</td>
                <td>{{ $v->model ?? '-' }}</td>
                <td> <a href="{{ route('vehicles.show', $v->id) }}" class="btn btn-sm btn-secondary">View</a> <a
                        href="{{ route('vehicles.edit', $v->id) }}" class="btn btn-sm btn-info">Edit</a> </td>
        </tr> @empty <tr>
                <td colspan="7" class="text-center">No vehicles found</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{ $vehicles->links() }}
