<x-layout title="Reports">
  <div class="pagetitle">
    <h1>Reports</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Statistics</a></li>
        <li class="breadcrumb-item active">Reports</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <form class="row g-3 pt-3" method="GET" action="{{ route('reports') }}">
          <div class="col-md-3">
            <label class="form-label">Report Type</label>
            <select class="form-select" name="type" required>
              <option value="" disabled {{ empty($type) ? 'selected' : '' }}>Select...</option>
              <option value="payments" {{ ($type ?? '')==='payments' ? 'selected' : '' }}>Payments</option>
              <option value="invoices" {{ ($type ?? '')==='invoices' ? 'selected' : '' }}>Sales Invoices</option>
              <option value="grn" {{ ($type ?? '')==='grn' ? 'selected' : '' }}>GRN</option>
              <option value="purchase_returns" {{ ($type ?? '')==='purchase_returns' ? 'selected' : '' }}>Purchase Returns</option>
              <option value="purchase_orders" {{ ($type ?? '')==='purchase_orders' ? 'selected' : '' }}>Purchase Orders</option>
              <option value="customers" {{ ($type ?? '')==='customers' ? 'selected' : '' }}>New Customers</option>
              <option value="vehicles" {{ ($type ?? '')==='vehicles' ? 'selected' : '' }}>New Vehicles</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="date_from" value="{{ $dateFrom ?? now()->subDays(30)->format('Y-m-d') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="date_to" value="{{ $dateTo ?? now()->format('Y-m-d') }}" required>
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i> Preview</button>
            @if(!empty($type))
              <a class="btn btn-primary" href="{{ route('reports.export', ['type'=>$type,'date_from'=>$dateFrom,'date_to'=>$dateTo,'format'=>'csv']) }}">
                <i class="bi bi-download"></i> Download CSV
              </a>
              <a class="btn btn-danger" href="{{ route('reports.export', ['type'=>$type,'date_from'=>$dateFrom,'date_to'=>$dateTo,'format'=>'pdf']) }}">
                <i class="bi bi-filetype-pdf"></i> Download PDF
              </a>
            @endif
          </div>
        </form>
        @if(!empty($type))
          <div class="table-responsive mt-4">
            <table class="table table-sm">
              <thead>
                <tr>
                  @foreach(($columns ?? []) as $col)
                    <th>{{ $col }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @forelse(($rows ?? []) as $row)
                  <tr>
                    @foreach($row as $cell)
                      <td>{{ is_numeric($cell) ? number_format($cell, 2) : $cell }}</td>
                    @endforeach
                  </tr>
                @empty
                  <tr><td colspan="{{ count($columns ?? []) }}" class="text-center">No data</td></tr>
                @endforelse
              </tbody>
            </table>
            @if(count($rows ?? []) >= 200)
              <p class="text-muted">Showing first 200 rows. Use download to get full data.</p>
            @endif
          </div>
        @endif
      </div>
    </div>
  </section>
</x-layout>

<x-layout title="Dashboard">
    <div class="pagetitle">
      <h1>Dashboard</h1>




      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      {{-- All your cards, charts, etc. here --}}
    </section>
  </x-layout>
