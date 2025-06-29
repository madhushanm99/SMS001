<x-layout>
  <x-slot name="title">Create Job Type</x-slot>

  <form method="POST" action="{{ route('jobtypes.store') }}">
    @csrf

    <div class="mb-3">
      <label for="jobType">Job Type Name</label>
      <input type="text" name="jobType" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="salesPrice">Sales Price</label>
      <input type="number" name="salesPrice" class="form-control" step="0.01" required>
    </div>

    <div class="mb-3">
      <label for="status">Status</label>
      <select name="status" class="form-control" required>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <div class="text-right">
      <button type="submit" class="btn btn-success">Save Job Type</button>
    </div>
  </form>
</x-layout>
