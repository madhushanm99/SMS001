<x-layout>
    <x-slot name="title">Payment Dashboard</x-slot>

    <div class="pagetitle">
        <h1>Payment Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <!-- Summary Cards Row -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Cash In</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-arrow-down-circle text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-success">Rs. {{ number_format($summary['today']['cash_in'], 2) }}</h6>
                                <span class="text-muted small pt-1">{{ $summary['today']['transactions'] }} transactions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Cash Out</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-arrow-up-circle text-danger"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-danger">Rs. {{ number_format($summary['today']['cash_out'], 2) }}</h6>
                                <span class="text-muted small pt-1">{{ $summary['today']['transactions'] }} transactions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Net Flow Today</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-graph-{{ ($summary['today']['cash_in'] - $summary['today']['cash_out']) >= 0 ? 'up' : 'down' }}-arrow"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-{{ ($summary['today']['cash_in'] - $summary['today']['cash_out']) >= 0 ? 'success' : 'danger' }}">
                                    Rs. {{ number_format($summary['today']['cash_in'] - $summary['today']['cash_out'], 2) }}
                                </h6>
                                <span class="text-muted small pt-1">
                                    {{ ($summary['today']['cash_in'] - $summary['today']['cash_out']) >= 0 ? 'Positive' : 'Negative' }} flow
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Approvals</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock text-warning"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-warning">{{ count($pendingTransactions) }}</h6>
                                <span class="text-muted small pt-1">Need attention</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Cash Flow Chart -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cash Flow Trend <span>| Last 7 Days</span></h5>
                        <div id="cashFlowChart"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">This Month Summary</h5>

                        <div class="activity">
                            <div class="activity-item d-flex">
                                <div class="activite-label">Cash In</div>
                                <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                                <div class="activity-content">
                                    Rs. {{ number_format($summary['month']['cash_in'], 2) }}
                                    <small class="text-muted d-block">{{ $summary['month']['transactions'] }} transactions</small>
                                </div>
                            </div>

                            <div class="activity-item d-flex">
                                <div class="activite-label">Cash Out</div>
                                <i class="bi bi-circle-fill activity-badge text-danger align-self-start"></i>
                                <div class="activity-content">
                                    Rs. {{ number_format($summary['month']['cash_out'], 2) }}
                                    <small class="text-muted d-block">{{ $summary['month']['transactions'] }} transactions</small>
                                </div>
                            </div>

                            <div class="activity-item d-flex">
                                <div class="activite-label">Net Flow</div>
                                <i class="bi bi-circle-fill activity-badge text-{{ ($summary['month']['cash_in'] - $summary['month']['cash_out']) >= 0 ? 'success' : 'warning' }} align-self-start"></i>
                                <div class="activity-content">
                                    Rs. {{ number_format($summary['month']['cash_in'] - $summary['month']['cash_out'], 2) }}
                                    <small class="text-muted d-block">
                                        {{ ($summary['month']['cash_in'] - $summary['month']['cash_out']) >= 0 ? 'Positive' : 'Negative' }} flow
                                    </small>
                                </div>
                            </div>

                            <div class="activity-item d-flex">
                                <div class="activite-label">Bank Balance</div>
                                <i class="bi bi-circle-fill activity-badge text-info align-self-start"></i>
                                <div class="activity-content">
                                    Rs. {{ number_format($summary['bank_balances'], 2) }}
                                    <small class="text-muted d-block">Total across all accounts</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Transactions -->
        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('payment-transactions.create', ['type' => 'cash_in']) }}"
                                   class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i><br>
                                    <small>Cash In</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('payment-transactions.create', ['type' => 'cash_out']) }}"
                                   class="btn btn-danger w-100">
                                    <i class="bi bi-plus-circle"></i><br>
                                    <small>Cash Out</small>
                                </a>
                            </div>

                            <div class="col-12">
                                <a href="{{ route('payment-transactions.index') }}"
                                   class="btn btn-outline-primary w-100">
                                    <i class="bi bi-list-ul"></i> View All Transactions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Transactions <span>| Last 7 Days</span></h5>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <small>{{ $transaction->transaction_date->format('M d') }}</small>
                                            </td>
                                            <td>
                                                @if($transaction->type === 'cash_in')
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-arrow-down"></i>
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        <i class="bi bi-arrow-up"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="small">{{ Str::limit($transaction->description, 30) }}</strong>
                                                    @if($transaction->paymentCategory)
                                                        <br><small class="text-muted">{{ $transaction->paymentCategory->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-{{ $transaction->type === 'cash_in' ? 'success' : 'danger' }}">
                                                    {{ $transaction->type === 'cash_out' ? '-' : '+' }}{{ number_format($transaction->amount, 0) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('completed')
                                                        <span class="badge bg-success">Done</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('payment-transactions.show', $transaction) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-3">
                                                <div class="text-muted">
                                                    <i class="bi bi-receipt"></i>
                                                    <p class="mb-0">No recent transactions</p>
                                                    <a href="{{ route('payment-transactions.create') }}" class="btn btn-sm btn-primary mt-2">
                                                        Create First Transaction
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        @if($pendingTransactions->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pending Approvals <span class="badge bg-warning">{{ $pendingTransactions->count() }}</span></h5>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Transaction #</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingTransactions as $transaction)
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">{{ $transaction->transaction_no }}</strong>
                                                </td>
                                                <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                                <td>
                                                    @if($transaction->type === 'cash_in')
                                                        <span class="badge bg-success">Cash In</span>
                                                    @else
                                                        <span class="badge bg-danger">Cash Out</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($transaction->description, 40) }}</td>
                                                <td>
                                                    <strong class="text-{{ $transaction->type === 'cash_in' ? 'success' : 'danger' }}">
                                                        {{ $transaction->type === 'cash_out' ? '-' : '+' }}Rs. {{ number_format($transaction->amount, 2) }}
                                                    </strong>
                                                </td>
                                                <td>{{ $transaction->created_by ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-success btn-approve"
                                                                data-id="{{ $transaction->id }}" title="Approve">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <a href="{{ route('payment-transactions.show', $transaction) }}"
                                                           class="btn btn-sm btn-outline-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger btn-cancel"
                                                                data-id="{{ $transaction->id }}" title="Cancel">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cash Flow Chart
            const cashFlowData = @json($summary['daily_data'] ?? []);

            if (cashFlowData.length > 0) {
                const chartOptions = {
                    series: [{
                        name: 'Cash In',
                        data: cashFlowData.map(d => d.cash_in || 0),
                        color: '#28a745'
                    }, {
                        name: 'Cash Out',
                        data: cashFlowData.map(d => d.cash_out || 0),
                        color: '#dc3545'
                    }],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                    },
                    markers: {
                        size: 4
                    },
                    colors: ['#28a745', '#dc3545'],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: cashFlowData.map(d => new Date(d.date).toLocaleDateString('en', { month: 'short', day: 'numeric' })),
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return "Rs. " + val.toLocaleString();
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return "Rs. " + val.toLocaleString();
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#cashFlowChart"), chartOptions);
                chart.render();
            } else {
                document.querySelector("#cashFlowChart").innerHTML =
                    '<div class="text-center text-muted py-4"><i class="bi bi-graph-up fs-1"></i><p>No data available for chart</p></div>';
            }

            // Pending transaction actions
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-approve')) {
                    e.preventDefault();
                    const id = e.target.closest('.btn-approve').dataset.id;
                    approveTransaction(id);
                }

                if (e.target.closest('.btn-cancel')) {
                    e.preventDefault();
                    const id = e.target.closest('.btn-cancel').dataset.id;
                    cancelTransaction(id);
                }
            });
        });

        function approveTransaction(id) {
            Swal.fire({
                title: 'Approve Transaction?',
                text: 'This action cannot be undone.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Approved!', data.success, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }

        function cancelTransaction(id) {
            Swal.fire({
                title: 'Cancel Transaction?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Cancelled!', data.success, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-layout>
