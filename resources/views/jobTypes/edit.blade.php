<x-layout>
  <x-slot name="title">Edit Job Type - {{ $jobtype->jobType }}</x-slot>

  <form method="POST" action="{{ route('jobtypes.update', $jobtype->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="jobType">Job Type Name</label>
      <input type="text" name="jobType" class="form-control" value="{{ $jobtype->jobType }}" required>
    </div>

    <div class="mb-3">
      <label for="salesPrice">Sales Price</label>
      <input type="number" name="salesPrice" class="form-control" value="{{ $jobtype->salesPrice }}" step="0.01" required>
    </div>

    <div class="mb-3">
      <label for="status">Status</label>
      <select name="status" class="form-control" required>
        <option value="1" @selected($jobtype->status == 1)>Active</option>
        <option value="0" @selected($jobtype->status == 0)>Inactive</option>
      </select>
    </div>

    <div class="text-right">
      <button type="submit" class="btn btn-success">Update Job Type</button>
    </div>
  </form>
</x-layout>
