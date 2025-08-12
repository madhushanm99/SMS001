<x-layout title="Add New Product">
    <x-slot name="title">Add New Product</x-slot>

    <h1>Add New Product</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="item_ID" class="form-label">Item ID</label>
                    <input type="text" name="item_ID" id="item_ID" class="form-control"
                        value="{{ old('item_ID') }}">
                </div>

                <div class="mb-3">
                    <label for="item_Name" class="form-label">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="item_Name" id="item_Name" class="form-control" required
                        value="{{ old('item_Name') }}">
                </div>

                <div class="mb-3">
                    <label for="product_Type" class="form-label">Product Type</label>
                    <select name="product_Type" id="product_Type" class="form-control">
                        <option value="">Select Product Type</option>
                        <option value="Genuine" {{ old('product_Type') == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                        <option value="After Marcket" {{ old('product_Type') == 'After Marcket' ? 'selected' : '' }}>After Marcket</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="catagory_Name" class="form-label">Category</label>
                    <select name="catagory_Name" id="catagory_Name" class="form-control">
                        <option value="">Select Category</option>
                        <option value="Spare" {{ old('catagory_Name') == 'Spare' ? 'selected' : '' }}>Spare</option>
                        <option value="Oil" {{ old('catagory_Name') == 'Oil' ? 'selected' : '' }}>Oil</option>
                        <option value="Electric" {{ old('catagory_Name') == 'Electric' ? 'selected' : '' }}>Electric</option>
                        <option value="Modify" {{ old('catagory_Name') == 'Modify' ? 'selected' : '' }}>Modify</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sales_Price" class="form-label">Sales Price</label>
                    <input type="number" step="0.01" name="sales_Price" id="sales_Price" class="form-control"
                        value="{{ old('sales_Price') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="units" class="form-label">Qty per Selling Units</label>
                    <input type="number" name="units" id="units" class="form-control"
                        value="{{ old('units') }}">
                </div>

                <div class="mb-3">
                    <label for="reorder_level" class="form-label">Re-order Level</label>
                    <input type="number" name="reorder_level" id="reorder_level" class="form-control"
                        value="{{ old('reorder_level') }}">
                </div>

                <div class="mb-3">
                    <label for="unitofMeture" class="form-label">Unit of Measure</label>
                    <select name="unitofMeture" id="unitofMeture" class="form-control">
                        <option value="">Select Unit</option>
                        <option value="Item" {{ old('unitofMeture') == 'Item' ? 'selected' : '' }}>Item</option>
                        <option value="ml" {{ old('unitofMeture') == 'ml' ? 'selected' : '' }}>ml</option>
                        <option value="g" {{ old('unitofMeture') == 'g' ? 'selected' : '' }}>g</option>
                        <option value="L" {{ old('unitofMeture') == 'L' ? 'selected' : '' }}>L</option>
                        <option value="KG" {{ old('unitofMeture') == 'KG' ? 'selected' : '' }}>KG</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-control">
                        <option value="" >Select Location</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->location_Name }}" {{ old('location') == $loc->location_Name ? 'selected' : '' }}>
                                {{ $loc->location_Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success mr-1">Add Product</button>
            <a href="{{ route('products') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</x-layout>
