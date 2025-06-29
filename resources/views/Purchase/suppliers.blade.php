<x-layout title="Supplier">
    <x-slot name="title">Supplier</x-slot>
    <div class="pagetitle">
        <h1>Supplier</h1>


        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Supplier</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="container">
            <div class="d-flex mb-3">

                <div class="col-md-6">
                    <form method="GET" action="{{ route('suppliers') }}" class="flex-grow-1 me-2">
                        <input type="text" id="search" name="search" class="form-control"
                            placeholder="Search by Supplier Name, Company, Phone or Email" autocomplete="off" />
                    </form>
                </div>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary fontSize14">New Supplier</a>
            </div>

            <div id="supplier-table">
                @include('Purchase.partials.suppliers_table', ['suppliers' => $suppliers])
            </div>

        </div>


    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                let query = $(this).val();

                $.ajax({
                    url: "{{ route('suppliers') }}", // same route
                    type: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#supplier-table').html(data);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });
        });
    </script>

</x-layout>
