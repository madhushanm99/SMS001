<x-layout title="Sales Invoices">
    <div class="pagetitle">
        <h1>Sales Invoices</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Sales Invoices</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Sales Invoices</h5>
                            <a href="{{ route('sales_invoices.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create Invoice
                            </a>
                        </div>

                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('sales_invoices.index') }}" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Invoice No, Customer...">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="hold" {{ request('status') == 'hold' ? 'selected' : '' }}>Hold</option>
                                        <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized</option>
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
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                        <a href="{{ route('sales_invoices.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Invoices Table -->
                        <div id="invoices-table">
                            @include('sales_invoices.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Are you sure you want to delete this invoice?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(invoiceId, status = 'hold') {
            const form = document.getElementById('deleteForm');
            form.action = `/sales-invoices/${invoiceId}`;
            
            const modalBody = document.getElementById('deleteModalBody');
            if (status === 'finalized') {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This is a finalized invoice!
                    </div>
                    <p>Are you sure you want to delete this finalized invoice?</p>
                    <p><strong>Note:</strong> Stock quantities will be restored when the invoice is deleted.</p>
                `;
            } else {
                modalBody.innerHTML = 'Are you sure you want to delete this invoice?';
            }
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function emailInvoice(invoiceId) {
            // Show confirmation dialog
            Swal.fire({
                icon: 'question',
                title: 'Send Invoice Email',
                text: 'Are you sure you want to email this invoice to the customer?',
                showCancelButton: true,
                confirmButtonText: 'Send Email',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Sending Email...',
                        text: 'Please wait while we send the invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/sales-invoices/${invoiceId}/email`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Email Sent!',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Email Failed',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to send email. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message
                            });
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-layout> 