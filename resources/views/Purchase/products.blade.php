<x-layout title="Dashboard">
    <x-slot name="title">Products</x-slot>

    <div class="pagetitle">
      <h1>Products</h1>




      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Products</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="container">
            <div class="d-flex mb-3">
                <form method="GET" action="{{ route('products') }}" class="flex-grow-1 me-2">
                    <input type="text" id="search" name="search" class="form-control"
                        placeholder="Search by Item Name or Item Code" autocomplete="off" />
                </form>
                <a href="{{ route('products.create') }}" class="btn btn-primary fontSize14">New Item</a>
            </div>

            <div id="item-table">
                @include('Purchase.partials.items_table', ['items' => $items])
            </div>

            <div class="d-flex justify-content-between mt-1">

            </div>

        </div>


    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                let query = $(this).val();

                $.ajax({
                    url: "{{ route('products') }}", // same route
                    type: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#item-table').html(data);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });
        });
    </script>
  </x-layout>
