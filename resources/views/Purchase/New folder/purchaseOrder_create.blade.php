<x-layout title="Add New Purchase Order">
    <h1>Add New Purchase Order</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <form id="product-form">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3 position-relative">
                    <label for="item_ID" class="form-label">Product ID</label>
                    <input type="text" name="item_ID" id="item_ID" class="form-control" autocomplete="off">
                    <div id="product_id_list" class="list-group"></div>
                </div>
                <div class="col-md-4 mb-3 ml-1">
                    <label for="item_Name" class="form-label">Product Name</label>
                    <input type="text" name="item_Name" id="item_Name" class="form-control" readonly>
                </div>
                <div class="col-md-2 mb-3 ml-1">
                    <label for="price" class="form-label">Selling Price</label>
                    <input type="text" name="price" id="price" class="form-control" readonly>
                </div>
                <div class="col-md-1 mb-3 ml-1">
                    <label for="qty" class="form-label">PO Qty</label>
                    <input type="text" name="qty" id="qty" class="form-control">
                </div>
                <div class="col-md-1 mb-3 ml-1">
                    <label for="Add" class="form-label">Add</label>
                    <button type="button" id="add-product-btn" class="btn btn-success mr-1">Add</button>
                </div>
            </div>
        </form>
    </div>

    <hr>
    <table class="table" id="product-lines-table">
        <thead>
            <tr>
                <th>Line No</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Selling Price</th>
                <th>Qty</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <hr>


    <div class="col-md-4 mb-3 position-relative">
        <label for="supplier_search" class="form-label">Supplier Search</label>
        <input type="text" id="supplier_search" class="form-control" autocomplete="off">
        <div id="supplier_list" class="list-group"></div>
    </div>

    <!-- Supplier Preview -->
    <div id="supplier_preview" class="mb-4 p-3 border rounded" style="display:none;">
        <h5>Supplier Details</h5>
        <p><strong>Name:</strong> <span id="supplier_name"></span></p>
        <p><strong>Company:</strong> <span id="supplier_company"></span></p>
        <p><strong>Phone:</strong> <span id="supplier_phone"></span></p>
        <p><strong>Address:</strong> <span id="supplier_address"></span></p>
        <input type="hidden" name="supp_Cus_ID" id="supp_Cus_ID">
    </div>

    <div class="text-end mt-4">
        <button type="button" id="create-po-btn" class="btn btn-primary">Create Purchase Order</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            let grandTotal = 0;
            // Autocomplete Product ID
            $('#item_ID').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 0) {
                    $.get('{{ route('autocomplete.product') }}', {
                        query
                    }, function(data) {
                        let list = '';
                        data.forEach(function(item) {
                            list +=
                                `<a href="#" class="list-group-item list-group-item-action product-id-item">${item}</a>`;
                        });
                        $('#product_id_list').html(list).show();
                    });
                } else {
                    $('#product_id_list').hide();
                }
            });

            // Select Product ID from dropdown
            $(document).on('click', '.product-id-item', function(e) {
                e.preventDefault();
                let item_ID = $(this).text();
                $('#item_ID').val(item_ID);
                $('#product_id_list').hide();

                // Fetch product name and selling price
                $.get(`/product-details/${item_ID}`, function(data) {
                    $('#item_Name').val(data.item_Name);
                    $('#price').val(data.sales_Price);
                    $('#item_Name').data('sales_Price', data.sales_Price);
                });
            });

            // Add Product Line
            $('#add-product-btn').on('click', function() {
                let item_ID = $('#item_ID').val();
                let qty = $('#qty').val();

                $.post('{{ route('add.product.line') }}', {
                    _token: '{{ csrf_token() }}',
                    item_ID: item_ID,
                    qty: qty
                }, function(response) {
                    if (response.success) {
                        loadProductLines();
                        $('#item_ID, #item_Name, #qty').val('');
                    } else {
                        alert('Error adding product line');
                    }
                });
            });

            // Load Product Lines
            function loadProductLines() {
                $.get('{{ route('get.product.lines') }}', function(lines) {
                    let rows = '';
                    grandTotal = 0;

                    lines.forEach(function(line) {
                        rows += `<tr>
                <td>${line.line_no}</td>
                <td>${line.item_ID}</td>
                <td>${line.item_Name}</td>
                <td>${line.sales_Price}</td>
                <td>${line.qty}</td>
                <td>${line.line_total}</td>
                </tr>`;

                        // Accumulate grand total (make sure line_total is treated as a number)
                        grandTotal += parseFloat(line.line_total) || 0;
                    });

                    // Append grand total row
                    rows += `<tr>
                    <td colspan="5" style="text-align: right; font-weight: bold;">Grand Total</td>
                    <td style="font-weight: bold;">${grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;

                    $('#product-lines-table tbody').html(rows);
                });
            }

            // Initial load
            loadProductLines();

            // Hide overlay when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#item_ID, #product_id_list').length) {
                    $('#product_id_list').hide();
                }
            });

            // Supplier Autocomplete
            $('#supplier_search').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.get('/autocomplete_supplier', {
                        query
                    }, function(data) {
                        let list = '';
                        data.forEach(function(supplier) {
                            list += `<a href="#" class="list-group-item list-group-item-action supplier-item"
                          data-id="${supplier.Supp_ID}">
                          ${supplier.Supp_Name} (${supplier.Company_Name})
                        </a>`;
                        });
                        $('#supplier_list').html(list).show();
                    });
                }
            });

            // Select Supplier
            $(document).on('click', '.supplier-item', function(e) {
                e.preventDefault();
                const suppID = $(this).data('id');
                $('#supplier_search').val($(this).text());
                $('#supplier_list').hide();

                // Fetch supplier details
                $.get(`/supplier-details/${suppID}`, function(data) {
                    $('#supplier_preview').show();
                    $('#supplier_name').text(data.Supp_Name);
                    $('#supplier_company').text(data.Company_Name);
                    $('#supplier_phone').text(data.Phone);
                    $('#supplier_address').text(data.Address1);
                    $('#supp_Cus_ID').val(data.Supp_CustomID); // Use Supp_CustomID as supp_Cus_ID
                });
            });

            $('#create-po-btn').on('click', function() {
                const poData = {
                    _token: '{{ csrf_token() }}',
                    supp_Cus_ID: $('#supp_Cus_ID').val(),
                    grand_Total: grandTotal, // Add grand total calculation
                    note: $('#note').val(),
                    Reff_No: $('#reff_no').val(),
                    emp_Name: '{{ auth()->user()->name }}' // Or get from input
                };

                $.post('/create-purchase-order', poData, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'PO created successfully!'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .fail(function(xhr) {
                        let errorMsg = 'An error occurred!';
                        // Try to get the error message from the response
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.responseText) {
                            // Sometimes responseJSON is undefined, try parsing responseText
                            try {
                                let res = JSON.parse(xhr.responseText);
                                if (res.error) errorMsg = res.error;
                            } catch (e) {
                                errorMsg = xhr.responseText;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    });
            });

        });
    </script>


</x-layout>
