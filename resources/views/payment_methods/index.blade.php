<x-layout>
    <x-slot name="title">Payment Methods</x-slot>

    <div class="pagetitle">
        <h1>Payment Methods</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment Methods</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Manage Payment Methods</h4>
            <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Payment Method
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Total Methods</h6>
                                <h4 class="text-white">{{ $paymentMethods->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-credit-card fs-2"></i>
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
                                <h6 class="card-title text-white">Active Methods</h6>
                                <h4 class="text-white">{{ $paymentMethods->where('is_active', true)->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-2"></i>
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
                                <h6 class="card-title text-white">Most Used</h6>
                                <h6 class="text-white">{{ $analytics['most_used']->name ?? 'N/A' }}</h6>
                                <small class="text-white-50">{{ $analytics['most_used']->usage_count ?? 0 }} times</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-star fs-2"></i>
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
                                <h6 class="card-title text-white">Require Reference</h6>
                                <h4 class="text-white">{{ $paymentMethods->where('requires_reference', true)->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-file-text fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Payment Methods</h5>

                <div class="table-responsive">
                    <table class="table table-hover" id="paymentMethodsTable">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Requires Reference</th>
                                <th>Status</th>
                                <th>Usage Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentMethods as $method)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @switch($method->code)
                                                @case('CASH')
                                                    <i class="bi bi-cash-coin text-success fs-4 me-2"></i>
                                                    @break
                                                @case('BANK_TRANSFER')
                                                    <i class="bi bi-bank2 text-primary fs-4 me-2"></i>
                                                    @break
                                                @case('CHECK')
                                                    <i class="bi bi-receipt text-warning fs-4 me-2"></i>
                                                    @break
                                                @case('CREDIT_CARD')
                                                    <i class="bi bi-credit-card text-info fs-4 me-2"></i>
                                                    @break
                                                @case('DIGITAL_WALLET')
                                                    <i class="bi bi-phone text-secondary fs-4 me-2"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-circle text-muted fs-4 me-2"></i>
                                            @endswitch
                                            <div>
                                                <strong>{{ $method->name }}</strong>
                                                <br><small class="text-muted">Created {{ $method->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="bg-light p-1 rounded">{{ $method->code }}</code>
                                    </td>
                                    <td>
                                        @if($method->description)
                                            {{ Str::limit($method->description, 50) }}
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->requires_reference)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-check-circle"></i> Required
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle"></i> Optional
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->is_active)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $analytics['usage_counts'][$method->id] ?? 0 }} uses</span>
                                        @if(($analytics['usage_counts'][$method->id] ?? 0) > 0)
                                            <br><small class="text-muted">
                                                Rs. {{ number_format($analytics['total_amounts'][$method->id] ?? 0, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('payment-methods.show', $method) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('payment-methods.edit', $method) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            @if(($analytics['usage_counts'][$method->id] ?? 0) === 0)
                                                <form action="{{ route('payment-methods.destroy', $method) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this payment method?')" 
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <button class="btn btn-sm btn-outline-{{ $method->is_active ? 'secondary' : 'success' }} btn-toggle-status" 
                                                    data-id="{{ $method->id }}" 
                                                    data-status="{{ $method->is_active ? 'inactive' : 'active' }}"
                                                    title="{{ $method->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi bi-{{ $method->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                                            <h5>No payment methods found</h5>
                                            <p>Add your first payment method to get started.</p>
                                            <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Payment Method
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

        <!-- Usage Analytics -->
        @if($paymentMethods->count() > 0)
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Usage by Payment Method</h5>
                            <div id="usageChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Transaction Volume by Method</h5>
                            <div id="volumeChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#paymentMethodsTable').DataTable({
                "pageLength": 10,
                "responsive": true,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [6] }
                ]
            });

            // Toggle status
            $('.btn-toggle-status').on('click', function() {
                const button = $(this);
                const id = button.data('id');
                const newStatus = button.data('status');
                
                Swal.fire({
                    title: `${newStatus === 'active' ? 'Activate' : 'Deactivate'} Payment Method?`,
                    text: `This will ${newStatus === 'active' ? 'enable' : 'disable'} this payment method for new transactions.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${newStatus === 'active' ? 'activate' : 'deactivate'} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/payment-methods/${id}/toggle-status`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Updated!', response.success, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', response.error, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                            }
                        });
                    }
                });
            });

            // Usage Analytics Charts
            @if($paymentMethods->count() > 0)
                // Usage Count Chart
                const usageData = @json($analytics['chart_data']['usage'] ?? []);
                if (usageData.length > 0) {
                    const usageChart = new ApexCharts(document.querySelector("#usageChart"), {
                        series: [{
                            data: usageData.map(d => d.count)
                        }],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        xaxis: {
                            categories: usageData.map(d => d.name)
                        },
                        yaxis: {
                            title: {
                                text: 'Number of Transactions'
                            }
                        },
                        colors: ['#28a745'],
                        dataLabels: {
                            enabled: true
                        }
                    });
                    usageChart.render();
                }

                // Volume Chart
                const volumeData = @json($analytics['chart_data']['volume'] ?? []);
                if (volumeData.length > 0) {
                    const volumeChart = new ApexCharts(document.querySelector("#volumeChart"), {
                        series: [{
                            data: volumeData.map(d => d.amount)
                        }],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        xaxis: {
                            categories: volumeData.map(d => d.name)
                        },
                        yaxis: {
                            title: {
                                text: 'Transaction Volume (Rs.)'
                            },
                            labels: {
                                formatter: function (val) {
                                    return "Rs. " + val.toLocaleString();
                                }
                            }
                        },
                        colors: ['#007bff'],
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                return "Rs. " + val.toLocaleString();
                            }
                        }
                    });
                    volumeChart.render();
                }
            @endif
        });
    </script>
    @endpush
</x-layout> 