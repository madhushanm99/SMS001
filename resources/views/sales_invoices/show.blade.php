<x-layout title="Invoice Details">
    <div class="pagetitle">
        <h1>Invoice #{{ $invoice->invoice_no }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales_invoices.index') }}">Sales Invoices</a></li>
                <li class="breadcrumb-item active">{{ $invoice->invoice_no }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Invoice Details</h5>
                            <div class="d-flex gap-2">
                                @if($invoice->status === 'finalized')
                                    <a href="{{ route('sales_invoices.pdf', $invoice->id) }}" 
                                       class="btn btn-outline-primary" target="_blank">
                                        <i class="bi bi-file-pdf"></i> View PDF
                                    </a>
                                    @if($invoice->customer && $invoice->customer->email)
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="emailInvoice({{ $invoice->id }})">
                                            <i class="bi bi-envelope"></i> Email Invoice
                                        </button>
                                    @endif
                                @endif
                                @if($invoice->status === 'hold' || ($invoice->status === 'finalized' && in_array(auth()->user()->usertype, ['admin', 'manager'])))
                                    <a href="{{ route('sales_invoices.edit', $invoice->id) }}" 
                                       class="btn btn-outline-warning">
                                        <i class="bi bi-pencil{{ $invoice->status === 'finalized' ? '-square' : '' }}"></i> 
                                        {{ $invoice->status === 'finalized' ? 'Edit Finalized Invoice' : 'Edit' }}
                                    </a>
                                @endif
                                @if($invoice->status === 'hold' || ($invoice->status === 'finalized' && in_array(auth()->user()->usertype, ['admin', 'manager'])))
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete({{ $invoice->id }}, '{{ $invoice->status }}')">
                                        <i class="bi bi-trash"></i> 
                                        {{ $invoice->status === 'finalized' ? 'Delete Finalized Invoice' : 'Delete' }}
                                    </button>
                                @endif
                                <a href="{{ route('sales_invoices.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        <!-- Invoice Header -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Invoice Information</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Invoice No:</strong></td>
                                        <td>{{ $invoice->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->status_color }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $invoice->created_by }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $invoice->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $invoice->customer->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $invoice->customer->email ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $invoice->customer->address ?: 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($invoice->notes)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6>Notes</h6>
                                    <div class="alert alert-light">
                                        {{ $invoice->notes }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Invoice Items -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Discount (%)</th>
                                        <th>Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td>{{ $item->line_no }}</td>
                                            <td>
                                                <strong>{{ $item->item_name }}</strong><br>
                                                <small class="text-muted">{{ $item->item_id }}</small>
                                            </td>
                                            <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->discount }}%</td>
                                            <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <th colspan="5" class="text-end">Grand Total:</th>
                                        <th>Rs. {{ number_format($invoice->grand_total, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
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
    </script>
    @endpush
</x-layout> 