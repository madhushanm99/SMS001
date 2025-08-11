<x-layout>
    <x-slot name="title">Bank Accounts</x-slot>

    <div class="pagetitle">
        <h1>Bank Accounts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bank Accounts</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Manage Bank Accounts</h4>
            <div class="btn-group">
                <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Account
                </a>
                <a href="{{ route('bank-accounts.reconcileIndex') }}" class="btn btn-warning">
                    <i class="bi bi-check2-square"></i> Reconcile
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Total Accounts</h6>
                                <h4 class="text-white">{{ $bankAccounts->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-bank2 fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Total Balance</h6>
                                <h4 class="text-white">{{ number_format($summary['total_balance'], 2) }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-cash-stack fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Active Accounts</h6>
                                <h4 class="text-white">{{ $summary['active_accounts'] }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Needs Reconciliation</h6>
                                <h4 class="text-white">{{ $summary['unreconciled_count'] }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Accounts Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bank Accounts</h5>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Bank</th>
                                <th>Type</th>
                                <th>Account Number</th>
                                <th>Current Balance</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankAccounts as $account)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle p-2 me-2">
                                                <i class="bi bi-bank"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $account->account_name }}</strong>
                                                <br><small class="text-muted">Added {{ $account->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $account->bank_name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $account->account_type)) }}</span>
                                    </td>
                                    <td>
                                        <code class="bg-light p-1 rounded">{{ $account->account_number }}</code>
                                    </td>
                                    <td>
                                        <strong class="text-{{ $account->current_balance >= 0 ? 'success' : 'danger' }}">
                                            {{ $account->current_balance >= 0 ? '+' : '' }}{{ number_format($account->current_balance, 2) }}
                                        </strong>
                                        @if($account->opening_balance != $account->current_balance)
                                            <br><small class="text-muted">
                                                Opening: {{ number_format($account->opening_balance, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $account->currency }}</span>
                                    </td>
                                    <td>
                                        @if($account->is_active)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Inactive
                                            </span>
                                        @endif

                                        @if($account->needs_reconciliation)
                                            <br><span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Needs Reconciliation
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('bank-accounts.show', $account) }}"
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('bank-accounts.statement', $account) }}"
                                               class="btn btn-sm btn-outline-info" title="View Statement">
                                                <i class="bi bi-file-text"></i>
                                            </a>
                                            <a href="{{ route('bank-accounts.edit', $account) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($account->needs_reconciliation)
                                                <a href="{{ route('bank-accounts.reconcile', $account) }}"
                                                   class="btn btn-sm btn-outline-warning" title="Reconcile">
                                                    <i class="bi bi-check2-square"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-bank2 fs-1 d-block mb-2"></i>
                                            <h5>No bank accounts found</h5>
                                            <p>Add your first bank account to start tracking balances.</p>
                                            <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Bank Account
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

        <!-- Balance Analytics -->
        @if($bankAccounts->count() > 0)
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Balance Distribution</h5>
                            <div id="balanceChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recent Account Activity</h5>
                            <div class="activity">
                                @forelse($recentActivity as $activity)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $activity->transaction_date->format('M d') }}</div>
                                        <i class="bi bi-circle-fill activity-badge text-{{ $activity->type === 'cash_in' ? 'success' : 'danger' }} align-self-start"></i>
                                        <div class="activity-content">
                                            <strong>{{ $activity->bankAccount->account_name }}</strong>
                                            <br>{{ Str::limit($activity->description, 40) }}
                                            <br><span class="text-{{ $activity->type === 'cash_in' ? 'success' : 'danger' }}">
                                                {{ $activity->type === 'cash_in' ? '+' : '-' }}Rs. {{ number_format($activity->amount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <p>No recent activity</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @push('scripts')
    <script>
        $(document).ready(function() {
            @if($bankAccounts->count() > 0)
                // Balance Distribution Chart
                const balanceData = @json($chartData['balances'] ?? []);
                if (balanceData.length > 0) {
                    const balanceChart = new ApexCharts(document.querySelector("#balanceChart"), {
                        series: balanceData.map(d => d.balance),
                        chart: {
                            type: 'donut',
                            height: 350
                        },
                        labels: balanceData.map(d => d.account_name),
                        colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                return val.toFixed(1) + '%';
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return "Rs. " + val.toLocaleString();
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    });
                    balanceChart.render();
                }
            @endif
        });
    </script>
    @endpush
</x-layout>
