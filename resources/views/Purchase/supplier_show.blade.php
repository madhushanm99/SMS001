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
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Supplier Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Purchase History</a>
                </li>
            </ul>

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
                                    <label>Last Visit</label>
                                    <input type="text" class="form-control" value="{{ $supplier->Last_GRN }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Total Spents</label>
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
