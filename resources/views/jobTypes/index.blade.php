<x-layout>
  <x-slot name="title">Job Types</x-slot>

  <div class="mb-3 d-flex justify-content-between">
    <h4>Job Types</h4>
    <a href="{{ route('jobtypes.create') }}" class="btn btn-primary">+ New Job Type</a>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <input type="text" id="search_box" class="form-control" placeholder="Search by Job Type" />
    </div>
  </div>

  <div id="jobtype_table_wrapper">
    @include('jobTypes.table', ['jobtypes' => $jobtypes])
  </div>

  @push('scripts')
    <script>
      document.getElementById('search_box').addEventListener('input', function () {
        const searchTerm = this.value;
        fetch(`{{ route('jobtypes.index') }}?search=${searchTerm}`)
          .then(response => response.text())
          .then(data => {
            const dom = new DOMParser().parseFromString(data, 'text/html');
            const newTable = dom.getElementById('jobtype_table_wrapper').innerHTML;
            document.getElementById('jobtype_table_wrapper').innerHTML = newTable;
          });
      });
    </script>
  @endpush
</x-layout>
