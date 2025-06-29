<x-layout>
  <x-slot name="title">Job Type Details - {{ $jobtype->jobType }}</x-slot>

  <div class="card">
    <div class="card-header">
      <h5>{{ $jobtype->jobType }}</h5>
    </div>
    <div class="card-body">
      <p><strong>Sales Price:</strong> Rs. {{ number_format($jobtype->salesPrice, 2) }}</p>
      <p><strong>Status:</strong>
        @if ($jobtype->status)
          <span class="badge badge-success">Active</span>
        @else
          <span class="badge badge-secondary">Inactive</span>
        @endif
      </p>
    </div>
    <div class="card-footer">
      <a href="{{ route('jobtypes.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>
  </div>
</x-layout>
