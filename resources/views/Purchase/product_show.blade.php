<x-layout title="Dashboard">
    <x-slot name="title">Products</x-slot>

    <div class="pagetitle">
        <h1>Supplier</h1>


        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products') }}">Products</a></li>
                <li class="breadcrumb-item active">{{ $item->item_Name }}</li>

            </ol>
        </nav>
    </div>

    <section class="section dashboard">



        <div class="container mt-4">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Item Details</a>
                </li>
            </ul>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-2">BASIC INFORMATION</h5>
                        <div>
                            <a href="{{ route('products.edit', $item->item_ID_Auto) }}">
                                <button type="button" class="btn btn-info me-2">Edit Details</button>
                            </a>
                        </div>
                    </div>

                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Item Name</label>
                                    <input type="text" class="form-control" value="{{ $item->item_Name }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Item ID</label>
                                    <input type="text" class="form-control" value="{{ $item->item_ID }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Product Type</label>
                                    <input type="text" class="form-control" value="{{ $item->product_Type }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Category</label>
                                    <input type="text" class="form-control" value="{{ $item->catagory_Name }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Salling Price</label>
                                    <input type="text" class="form-control" value="{{ $item->sales_Price }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Qty per Selling Units</label>
                                    <input type="text" class="form-control" value="{{ $item->units }}"
                                        disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Unit of Measure</label>
                                    <input type="email" class="form-control" value="{{ $item->unitofMeture }}" disabled>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Location</label>
                                    <input type="text" class="form-control" value="{{ $item->location }}"
                                        disabled>
                                </div>

                            </div>
                        </div>

                    </form>
                    <div class="d-flex justify-content-between">
                        <form method="POST" action="{{ route('products.delete', $item->item_ID_Auto) }}"
                            id="delete-form-{{ $item->item_ID_Auto }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger"
                                onclick="confirmDelete({{ $item->item_ID_Auto }})">
                                Delete Item
                            </button>
                        </form>

                        <a href="{{ route('products') }}">
                            <button type="button" class="btn btn-success">Back to Products</button>
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
