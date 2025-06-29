<x-layout title="Invoice Returns">
    <div class="pagetitle">
        <h1>Invoice Returns</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Invoice Returns</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Invoice Returns</h5>
                            <a href="{{ route('invoice_returns.select') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create Return
                            </a>
                        </div>

                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('invoice_returns.index') }}" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Return No, Invoice No, Customer...">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date" 
                                           value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date" 
                                           value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Filter
                                        </button>
                                        <a href="{{ route('invoice_returns.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Returns Table -->
                        <div id="returns-table-container">
                            @include('invoice_returns.table', ['returns' => $returns])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Auto-submit form on filter changes (optional)
            $('#search, #status, #from_date, #to_date').on('change', function() {
                // Uncomment the line below to enable auto-filtering
                // $(this).closest('form').submit();
            });
        });

        function viewReturn(returnId) {
            window.location.href = '{{ route("invoice_returns.show", ":id") }}'.replace(':id', returnId);
        }

        function printReturn(returnId) {
            window.open('{{ route("invoice_returns.pdf", ":id") }}'.replace(':id', returnId), '_blank');
        }
    </script>
    @endpush
</x-layout> 