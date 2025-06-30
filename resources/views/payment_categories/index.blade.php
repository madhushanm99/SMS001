<x-layout>
    <x-slot name="title">Payment Categories</x-slot>

    <div class="pagetitle">
        <h1>Payment Categories</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment Categories</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Manage Payment Categories</h4>
            <a href="{{ route('payment-categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Category
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Total Categories</h6>
                                <h4 class="text-white">{{ $categories->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-tags fs-2"></i>
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
                                <h6 class="card-title text-white">Income Categories</h6>
                                <h4 class="text-white">{{ $categories->where('type', 'income')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-down-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Expense Categories</h6>
                                <h4 class="text-white">{{ $categories->where('type', 'expense')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-up-circle fs-2"></i>
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
                                <h6 class="card-title text-white">Parent Categories</h6>
                                <h4 class="text-white">{{ $categories->whereNull('parent_id')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-folder fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Type Tabs -->
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            <i class="bi bi-tags"></i> All Categories ({{ $categories->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button" role="tab">
                            <i class="bi bi-arrow-down-circle text-success"></i> Income ({{ $categories->where('type', 'income')->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense" type="button" role="tab">
                            <i class="bi bi-arrow-up-circle text-danger"></i> Expense ({{ $categories->where('type', 'expense')->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- All Categories -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        @include('payment_categories.table', ['categories' => $categories, 'analytics' => $analytics])
                    </div>

                    <!-- Income Categories -->
                    <div class="tab-pane fade" id="income" role="tabpanel">
                        @include('payment_categories.table', ['categories' => $categories->where('type', 'income'), 'analytics' => $analytics])
                    </div>

                    <!-- Expense Categories -->
                    <div class="tab-pane fade" id="expense" role="tabpanel">
                        @include('payment_categories.table', ['categories' => $categories->where('type', 'expense'), 'analytics' => $analytics])
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Analytics -->
        @if($categories->count() > 0)
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Category Usage</h5>
                            <div id="usageChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Transaction Volume by Category</h5>
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
            @if($categories->count() > 0)
                // Usage Chart
                const usageData = @json($chartData['usage'] ?? []);
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
                        colors: ['#007bff'],
                        dataLabels: {
                            enabled: true
                        }
                    });
                    usageChart.render();
                }

                // Volume Chart
                const volumeData = @json($chartData['volume'] ?? []);
                if (volumeData.length > 0) {
                    const volumeChart = new ApexCharts(document.querySelector("#volumeChart"), {
                        series: [{
                            name: 'Income',
                            data: volumeData.filter(d => d.type === 'income').map(d => d.amount)
                        }, {
                            name: 'Expense',
                            data: volumeData.filter(d => d.type === 'expense').map(d => d.amount)
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            stacked: true
                        },
                        xaxis: {
                            categories: [...new Set(volumeData.map(d => d.name))]
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
                        colors: ['#28a745', '#dc3545'],
                        dataLabels: {
                            enabled: false
                        }
                    });
                    volumeChart.render();
                }
            @endif
        });
    </script>
    @endpush
</x-layout> 