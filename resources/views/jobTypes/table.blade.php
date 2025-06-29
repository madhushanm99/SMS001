<table class="table table-bordered table-sm text-sm">
  <thead>
    <tr>
      <th>Job Type</th>
      <th>Sales Price</th>
      <th>Status</th>
      <th class="text-center">Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($jobtypes as $jobtype)
      <tr>
        <td>{{ $jobtype->jobType }}</td>
        <td>Rs. {{ number_format($jobtype->salesPrice, 2) }}</td>
        <td>
          @if ($jobtype->status)
            <span class="badge badge-success">Active</span>
          @else
            <span class="badge badge-secondary">Inactive</span>
          @endif
        </td>
        <td class="text-center">
          <a href="{{ route('jobtypes.show', $jobtype->id) }}" class="btn btn-sm btn-info">View</a>
          <a href="{{ route('jobtypes.edit', $jobtype->id) }}" class="btn btn-sm btn-warning">Edit</a>
          <form action="{{ route('jobtypes.destroy', $jobtype->id) }}" method="POST" class="d-inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

{{ $jobtypes->links() }}
