<x-layout title="Dashboard">
    <x-slot name="title">Supplier</x-slot>

    <div class="pagetitle">
        <h1>Supplier</h1>


        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('suppliers') }}">Supplier</a></li>
                <li class="breadcrumb-item active">{{ $supplier->Company_Name }}</li>

            </ol>
        </nav>
    </div>

    <section class="section dashboard">



        <div class="container mt-4">
            <ul class="nav nav-tabs mb-3" id="supplierTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="details-tab" data-bs-toggle="tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Supplier Details</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="history-tab" data-bs-toggle="tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">Purchase History</a>
                </li>
            </ul>

            <div class="tab-content" id="supplierTabsContent">
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card">
                        <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-2">BASIC INFORMATION</h5>
                        <div>
                            <a href="{{ route('suppliers.edit', $supplier->Supp_ID) }}">
                                <button type="button" class="btn btn-info me-2">Edit Details</button>
                            </a>
                            <button type="button" class="btn btn-secondary"
                                disabled>{{ $supplier->Supp_Group_Name }}</button>
                        </div>
                    </div>

                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>SUPPLIER NAME</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Supp_Name }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>SUPPLIER CODE</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Supp_CustomID }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>MOBILE NUMBER</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Phone }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>FAX NUMBER</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Fax }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Total Orders</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Total_Orders }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Outstanding Purchase Return Credit</label>
                                    <input type="text" class="form-control" value="{{ number_format($supplier->getOutstandingPurchaseReturnCredit(), 2) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>COMPANY NAME</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Company_Name }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>E-MAIL ADDRESS</label>
                                    <input type="email" class="form-control" value="{{ $supplier->Email }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>ADDRESS</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Address1 }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Last GRN</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Last_GRN }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Total Spent</label>
                                    <input type="text" class="form-control"
                                        value="{{ number_format($supplier->Total_Spent, 2, '.', ',') }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>REMARK</label>
                            <textarea class="form-control" rows="3" disabled>{{ $supplier->Remark }}</textarea>
                        </div>
                        </form>
                        <div class="d-flex justify-content-between">
                            <form method="POST" action="{{ route('suppliers.delete', $supplier->Supp_ID) }}"
                                id="delete-form-{{ $supplier->Supp_ID }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger"
                                    onclick="confirmDelete({{ $supplier->Supp_ID }})">
                                    Delete Supplier
                                </button>
                            </form>

                            <a href="{{ route('suppliers') }}">
                                <button type="button" class="btn btn-success">Back to Supplier</button>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Recent GRNs</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>GRN No</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Outstanding</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($purchaseHistory ?? []) as $grn)
                                            @php
                                                $total = $grn->items->sum('line_total');
                                                $paid = $grn->paymentTransactions->sum('amount');
                                                $outstanding = $total - $paid;
                                            @endphp
                                            <tr>
                                                <td>{{ $grn->grn_no }}</td>
                                                <td>{{ $grn->grn_date }}</td>
                                                <td>{{ $grn->items->count() }}</td>
                                                <td>{{ number_format($total, 2) }}</td>
                                                <td>{{ number_format($paid, 2) }}</td>
                                                <td>{{ number_format($outstanding, 2) }}</td>
                                                <td>
                                                    @php $status = $grn->getPaymentStatus(); @endphp
                                                    <span class="badge bg-{{ $status === 'paid' ? 'success' : ($status === 'partially_paid' ? 'warning' : 'secondary') }}">{{ ucfirst(str_replace('_',' ', $status)) }}</span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('grns.pdf', $grn->grn_id) }}" target="_blank">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No GRNs found for this supplier.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            }
        </script>

</x-layout>
