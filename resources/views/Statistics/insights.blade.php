<x-layout title="Insights">
    <div class="pagetitle">
      <h1>Insights</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Insights</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body pt-3">
          <ul class="nav nav-tabs nav-tabs-bordered">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-payments">Payments</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-invoices">Invoices</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-grn">GRN</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-returns">Purchase Returns</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pos">Purchase Orders</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-customers">Customers</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vehicles">Vehicles</button></li>
          </ul>

          <div class="tab-content pt-2">
            <div class="tab-pane fade show active" id="tab-payments">
              <div class="row g-3">
                <div class="col-md-3"><div class="info-box"><h6>Total Cash In ({{ $days }}d)</h6><h3 class="text-success">{{ number_format($payments['cash_in'],2) }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>Total Cash Out ({{ $days }}d)</h6><h3 class="text-danger">{{ number_format($payments['cash_out'],2) }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>Net Flow</h6><h3>{{ number_format($payments['net'],2) }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>Txns (C/P)</h6><h3>{{ $payments['completed'] }}/{{ $payments['pending'] }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>No</th><th>Type</th><th>Amount</th><th>Method</th><th>Category</th><th>Customer/Supplier</th></tr></thead>
                  <tbody>
                    @forelse($payments['recent'] as $t)
                      <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($t->transaction_date)->format('Y-m-d') }}</td>
                        <td>{{ $t->transaction_no }}</td>
                        <td><span class="badge bg-{{ $t->isCashIn() ? 'success' : 'danger' }}">{{ $t->getTypeLabel() }}</span></td>
                        <td>{{ number_format($t->amount,2) }}</td>
                        <td>{{ $t->paymentMethod->name ?? '-' }}</td>
                        <td>{{ $t->paymentCategory->name ?? '-' }}</td>
                        <td>{{ $t->customer->name ?? $t->supplier->Supp_Name ?? '-' }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center">No recent transactions</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-invoices">
              <div class="row g-3">
                <div class="col-md-3"><div class="info-box"><h6>Invoices ({{ $days }}d)</h6><h3>{{ $invoices['count'] }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>Total Value</h6><h3>{{ number_format($invoices['total'],2) }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>Finalized</h6><h3>{{ $invoices['finalized'] }}</h3></div></div>
                <div class="col-md-3"><div class="info-box"><h6>On Hold</h6><h3>{{ $invoices['hold'] }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>No</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                  <tbody>
                    @forelse($invoices['recent'] as $inv)
                      <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($inv->invoice_date)->format('Y-m-d') }}</td>
                        <td>{{ $inv->invoice_no }}</td>
                        <td>{{ $inv->customer->name ?? '-' }}</td>
                        <td>{{ number_format($inv->grand_total,2) }}</td>
                        <td><span class="badge bg-{{ $inv->status_color ?? 'secondary' }}">{{ ucfirst($inv->status) }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No recent invoices</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-grn">
              <div class="row g-3">
                <div class="col-md-4"><div class="info-box"><h6>GRNs ({{ $days }}d)</h6><h3>{{ $grn['count'] }}</h3></div></div>
                <div class="col-md-4"><div class="info-box"><h6>Total Value</h6><h3>{{ number_format($grn['total'],2) }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>No</th><th>Supplier</th><th>Total</th><th>Status</th></tr></thead>
                  <tbody>
                    @forelse($grn['recent'] as $g)
                      <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($g->grn_date)->format('Y-m-d') }}</td>
                        <td>{{ $g->grn_no }}</td>
                        <td>{{ $g->supplier->Supp_Name ?? '-' }}</td>
                        <td>{{ number_format($g->items->sum('line_total'),2) }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($g->status ?? 'n/a') }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No recent GRNs</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-returns">
              <div class="row g-3">
                <div class="col-md-4"><div class="info-box"><h6>Purchase Returns ({{ $days }}d)</h6><h3>{{ $purchaseReturns['count'] }}</h3></div></div>
                <div class="col-md-4"><div class="info-box"><h6>Total Value</h6><h3>{{ number_format($purchaseReturns['total'],2) }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>No</th><th>Supplier</th><th>Total</th><th>Status</th></tr></thead>
                  <tbody>
                    @forelse($purchaseReturns['recent'] as $r)
                      <tr>
                        <td>{{ $r->created_at?->format('Y-m-d') }}</td>
                        <td>{{ $r->return_no }}</td>
                        <td>{{ $r->supplier->Supp_Name ?? '-' }}</td>
                        <td>{{ number_format($r->getTotalAmount(),2) }}</td>
                        <td><span class="badge bg-secondary">{{ $r->status ? 'Completed' : 'Pending' }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No recent purchase returns</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-pos">
              <div class="row g-3">
                <div class="col-md-4"><div class="info-box"><h6>Purchase Orders ({{ $days }}d)</h6><h3>{{ $purchaseOrders['count'] }}</h3></div></div>
                <div class="col-md-4"><div class="info-box"><h6>Total Value</h6><h3>{{ number_format($purchaseOrders['total'],2) }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>No</th><th>Supplier</th><th>Total</th><th>Status</th></tr></thead>
                  <tbody>
                    @forelse($purchaseOrders['recent'] as $po)
                      <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                        <td>{{ $po->po_No }}</td>
                        <td>{{ $po->supp_Cus_ID }}</td>
                        <td>{{ number_format($po->grand_Total,2) }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($po->status ?? 'n/a') }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No recent purchase orders</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-customers">
              <div class="row g-3">
                <div class="col-md-4"><div class="info-box"><h6>New Customers ({{ $days }}d)</h6><h3>{{ $customers['count'] }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>ID</th><th>Name</th><th>Phone</th><th>Email</th></tr></thead>
                  <tbody>
                    @forelse($customers['recent'] as $c)
                      <tr>
                        <td>{{ $c->created_at?->format('Y-m-d') }}</td>
                        <td>{{ $c->custom_id }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->phone }}</td>
                        <td>{{ $c->email }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No new customers</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="tab-vehicles">
              <div class="row g-3">
                <div class="col-md-4"><div class="info-box"><h6>New Vehicles ({{ $days }}d)</h6><h3>{{ $vehicles['count'] }}</h3></div></div>
                <div class="col-md-4"><div class="info-box"><h6>Approved</h6><h3>{{ $vehicles['approved'] }}</h3></div></div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-sm">
                  <thead><tr><th>Date</th><th>Vehicle No</th><th>Customer</th><th>Brand</th><th>Model</th><th>Approved</th></tr></thead>
                  <tbody>
                    @forelse($vehicles['recent'] as $v)
                      <tr>
                        <td>{{ $v->created_at?->format('Y-m-d') }}</td>
                        <td>{{ $v->vehicle_no }}</td>
                        <td>{{ $v->customer->name ?? '-' }}</td>
                        <td>{{ $v->brand->name ?? '-' }}</td>
                        <td>{{ $v->model }}</td>
                        <td><span class="badge bg-{{ $v->is_approved ? 'success' : 'secondary' }}">{{ $v->is_approved ? 'Yes' : 'No' }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="6" class="text-center">No new vehicles</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </x-layout>
