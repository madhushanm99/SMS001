<x-layout>
    <x-slot name="title">Invoice Finalized - Payment & PDF Options</x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Success Alert -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Invoice Summary Card -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                Invoice Finalized Successfully
                            </h5>
                            <span class="badge bg-light text-success fs-6">{{ $serviceInvoice->invoice_no }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p class="mb-1"><strong>{{ $serviceInvoice->customer->name }}</strong></p>
                                <p class="mb-1 text-muted">{{ $serviceInvoice->customer->phone }}</p>
                                @if($serviceInvoice->vehicle_no)
                                    <p class="mb-1">
                                        <span class="badge bg-info">{{ $serviceInvoice->vehicle_no }}</span>
                                        @if($serviceInvoice->mileage)
                                            <span class="badge bg-secondary ms-1">{{ number_format($serviceInvoice->mileage) }} km</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Invoice Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Job Types:</small>
                                        <div>Rs. {{ number_format($serviceInvoice->job_total, 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Spare Parts:</small>
                                        <div>Rs. {{ number_format($serviceInvoice->parts_total, 2) }}</div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="fs-5 fw-bold text-success">
                                    Total: Rs. {{ number_format($serviceInvoice->grand_total, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Cards -->
                <div class="row g-4">
                    
                    <!-- Payment Option -->
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Add Payment Record
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Record customer payment for this service invoice. You can add multiple payments if needed.</p>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Outstanding Amount:</small>
                                    <div class="fs-4 fw-bold text-primary">
                                        Rs. {{ number_format($serviceInvoice->getOutstandingAmount(), 2) }}
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('service_invoices.add_payment', $serviceInvoice) }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Add Payment
                                    </a>
                                    <small class="text-muted text-center">
                                        Record cash, card, or bank payments
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDF Options -->
                    <div class="col-md-6">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-pdf me-2"></i>
                                    PDF & Email Options
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Download, print, or email the service invoice to the customer.</p>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('service_invoices.pdf', $serviceInvoice) }}" class="btn btn-danger btn-lg" target="_blank">
                                        <i class="bi bi-download me-2"></i>
                                        Download PDF
                                    </a>
                                    
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#emailModal">
                                        <i class="bi bi-envelope me-2"></i>
                                        Email PDF
                                    </button>
                                    
                                    <button type="button" class="btn btn-outline-secondary" onclick="printInvoice()">
                                        <i class="bi bi-printer me-2"></i>
                                        Print Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>What would you like to do next?</h6>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('service_invoices.create') }}" class="btn btn-success">
                                        <i class="bi bi-plus me-2"></i>
                                        Create New Invoice
                                    </a>
                                    <a href="{{ route('service_invoices.show', $serviceInvoice) }}" class="btn btn-info">
                                        <i class="bi bi-eye me-2"></i>
                                        View Invoice Details
                                    </a>
                                    <a href="{{ route('service_invoices.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-list me-2"></i>
                                        Back to Invoice List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('service_invoices.email', $serviceInvoice) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Email Invoice PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ $serviceInvoice->customer->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3" 
                                      placeholder="Add a personal message...">Please find attached your service invoice.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function printInvoice() {
            // Open PDF in new window for printing
            const pdfUrl = '{{ route('service_invoices.pdf', $serviceInvoice) }}';
            const printWindow = window.open(pdfUrl, '_blank');
            
            printWindow.onload = function() {
                printWindow.print();
            };
        }

        // Auto-focus email field when modal opens
        document.getElementById('emailModal').addEventListener('shown.bs.modal', function () {
            document.getElementById('email').focus();
        });
    </script>
    @endpush
</x-layout> 