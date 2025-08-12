<table class="table table-bordered table-sm text-sm">
    <thead>
        <tr>
            <th>Reg No</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>NIC</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Next Service</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($vehicles as $v)
            <tr>
                <td>
                    {{ $v->vehicle_no }}
                    @if(!$v->is_approved)
                        <span class="badge bg-warning text-dark ms-2" title="Pending approval">Pending</span>
                    @endif
                </td>
                <td>{{ $v->customer->name }}</td>
                <td>{{ $v->customer->phone }}</td>
                <td>{{ $v->customer->nic }}</td>
                <td>{{ $v->brand->name ?? '-' }}</td>
                <td>{{ $v->model ?? '-' }}</td>
                <td>
                    @php($s = $v->serviceSchedule)
                    @if($s && ($s->next_service_date || $s->next_service_mileage))
                        <div>
                            Date: {{ $s->next_service_date ? $s->next_service_date->format('Y-m-d') : '—' }}
                        </div>
                        <div>
                            Mileage: {{ $s->next_service_mileage ? number_format($s->next_service_mileage) . ' km' : '—' }}
                        </div>
                    @else
                        <span class="text-muted">no data to show</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('vehicles.show', $v->id) }}" class="btn btn-sm btn-secondary">View</a>
                    <a href="{{ route('vehicles.edit', $v->id) }}" class="btn btn-sm btn-info">Edit</a>
                    @if(!$v->is_approved)
                        <form method="POST" action="{{ route('vehicles.approve', $v->id) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success" title="Approve vehicle">Approve</button>
                        </form>
                    @endif
                </td>
        </tr> @empty <tr>
                <td colspan="7" class="text-center">No vehicles found</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{ $vehicles->links() }}
