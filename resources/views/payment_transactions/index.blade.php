<x-layout>
    <x-slot name="title">Payment Transactions</x-slot>

    <div class="pagetitle">
        <h1>Payment Transactions</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment Transactions</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Cash In</h6>
                                <h4 class="text-white">{{ number_format($summary['cash_in'], 2) }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-down-circle-fill fs-2"></i>
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
                                <h6 class="card-title text-white">Cash Out</h6>
                                <h4 class="text-white">{{ number_format($summary['cash_out'], 2) }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-up-circle-fill fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-{{ $summary['net_flow'] >= 0 ? 'primary' : 'warning' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Net Flow</h6>
                                <h4 class="text-white">{{ number_format($summary['net_flow'], 2) }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-graph-{{ $summary['net_flow'] >= 0 ? 'up' : 'down' }}-arrow fs-2"></i>
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
                                <h6 class="card-title text-white">Total Transactions</h6>
                                <h4 class="text-white">{{ number_format($summary['total_transactions']) }}</h4>
                                @if($summary['pending_count'] > 0)
                                    <small class="text-white-50">{{ $summary['pending_count'] }} pending</small>
                                @endif
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-receipt fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Transaction History</h4>
            <div class="btn-group">
                <a href="{{ route('payment-transactions.create', ['type' => 'cash_in']) }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Cash In
                </a>
                <a href="{{ route('payment-transactions.create', ['type' => 'cash_out']) }}" class="btn btn-danger">
                    <i class="bi bi-plus-circle"></i> Cash Out
                </a>
                <a href="{{ route('payment-transactions.dashboard') }}" class="btn btn-info">
                    <i class="bi bi-graph-up"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-funnel"></i> Filters
                    <button class="btn btn-sm btn-outline-secondary float-right" type="button" data-toggle="collapse" data-target="#filterCollapse">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </h6>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form id="filter_form" class="row g-2">
                        <div class="col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Transaction #, description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">All Types</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="payment_method_id" class="form-label">Payment Method</label>
                            <select name="payment_method_id" id="payment_method_id" class="form-control">
                                <option value="">All Methods</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('payment-transactions.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-body">
                <div id="transactions_table_wrapper">
                    @include('payment_transactions.table', ['transactions' => $transactions])
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        const form = document.getElementById('filter_form');
        let typingTimer;

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchResults();
        });

        // Real-time search
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => fetchResults(), 500);
        });

        // Filter changes
        ['type', 'status', 'payment_method_id', 'date_from', 'date_to'].forEach(field => {
            document.getElementById(field).addEventListener('change', function() {
                fetchResults();
            });
        });

        function fetchResults(page = 1) {
            const params = new URLSearchParams(new FormData(form));
            if (page > 1) params.append('page', page);
            
            fetch(`{{ route('payment-transactions.index') }}?${params.toString()}`)
                .then(res => res.text())
                .then(html => {
                    const dom = new DOMParser().parseFromString(html, 'text/html');
                    const newTable = dom.getElementById('transactions_table_wrapper').innerHTML;
                    document.getElementById('transactions_table_wrapper').innerHTML = newTable;
                    
                    // Update URL without reload
                    window.history.replaceState({}, '', `{{ route('payment-transactions.index') }}?${params.toString()}`);
                })
                .catch(err => console.error('Filter error:', err));
        }

        // Pagination
        document.addEventListener('click', function(e) {
            if (e.target.matches('.pagination a')) {
                e.preventDefault();
                const url = new URL(e.target.href);
                const page = url.searchParams.get('page');
                fetchResults(page);
            }
        });

        // Transaction actions
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-approve')) {
                e.preventDefault();
                const id = e.target.closest('.btn-approve').dataset.id;
                approveTransaction(id);
            }
            
            if (e.target.closest('.btn-complete')) {
                e.preventDefault();
                const id = e.target.closest('.btn-complete').dataset.id;
                completeTransaction(id);
            }
            
            if (e.target.closest('.btn-cancel')) {
                e.preventDefault();
                const id = e.target.closest('.btn-cancel').dataset.id;
                cancelTransaction(id);
            }
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
                            Swal.fire('Approved!', data.success, 'success');
                            fetchResults();
                        } else {
                            Swal.fire('Error!', data.error, 'error');
                        }
                    });
                }
            });
        }

        function completeTransaction(id) {
            Swal.fire({
                title: 'Complete Transaction?',
                text: 'This will finalize the transaction.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/payment-transactions/${id}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Completed!', data.success, 'success');
                            fetchResults();
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
                            Swal.fire('Cancelled!', data.success, 'success');
                            fetchResults();
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