<x-layout title="Select Invoice for Return">
    <div class="pagetitle">
        <h1>Select Invoice for Return</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice_returns.index') }}">Invoice Returns</a></li>
                <li class="breadcrumb-item active">Select Invoice</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Select Sales Invoice</h5>
                        <p class="text-muted mb-4">Search and select a finalized sales invoice to process a return.</p>

                        <form id="invoice-select-form">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="invoice_search" class="form-label">Search Invoice *</label>
                                    <select id="invoice_search" name="invoice_id" class="form-select" required>
                                        <option value="">Search by Invoice No, Customer Name, or Phone...</option>
                                    </select>
                                    <div class="form-text">Only finalized invoices are available for returns</div>
                                </div>
                            </div>

                            <div id="invoice-details" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Selected Invoice Details</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Invoice No:</strong> <span id="selected-invoice-no"></span></p>
                                                <p><strong>Customer:</strong> <span id="selected-customer"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Date:</strong> <span id="selected-date"></span></p>
                                                <p><strong>Total:</strong> Rs. <span id="selected-total"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="button" id="proceed-btn" class="btn btn-primary" disabled>
                                    <i class="bi bi-arrow-right"></i> Proceed to Return Items
                                </button>
                                <a href="{{ route('invoice_returns.index') }}" class="btn btn-secondary ms-2">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            let selectedInvoice = null;

            // Initialize Select2 for invoice search
            $('#invoice_search').select2({
                ajax: {
                    url: '{{ route("invoice_returns.search_invoices") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Search by Invoice No, Customer Name, or Phone...'
            });

            // Handle invoice selection
            $('#invoice_search').on('select2:select', function (e) {
                selectedInvoice = e.params.data;
                showInvoiceDetails(selectedInvoice);
                $('#proceed-btn').prop('disabled', false);
            });

            // Handle invoice deselection
            $('#invoice_search').on('select2:unselect', function (e) {
                selectedInvoice = null;
                $('#invoice-details').hide();
                $('#proceed-btn').prop('disabled', true);
            });

            // Proceed to return creation
            $('#proceed-btn').click(function() {
                if (selectedInvoice) {
                    window.location.href = '{{ route("invoice_returns.create", ":id") }}'.replace(':id', selectedInvoice.id);
                }
            });

            function showInvoiceDetails(invoice) {
                $('#selected-invoice-no').text(invoice.invoice_no);
                $('#selected-customer').text(invoice.customer_name);
                $('#selected-date').text(invoice.invoice_date);
                $('#selected-total').text(numberWithCommas(invoice.grand_total));
                $('#invoice-details').fadeIn();
            }

            function numberWithCommas(x) {
                return parseFloat(x).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        });
    </script>

    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-selection {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }
        .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }
    </style>
    @endpush

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush
</x-layout> 