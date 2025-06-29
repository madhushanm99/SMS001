<x-layout title="Add New Supplier">
    <x-slot name="title">Add New Supplier</x-slot>
    <h1>Add New Supplier</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="Supp_CustomID" class="form-label">Custom ID</label>
                    <input type="text" name="Supp_CustomID" id="Supp_CustomID" class="form-control"
                        value="{{ old('Supp_CustomID') }}">
                </div>

                <div class="mb-3">
                    <label for="Supp_Name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" name="Supp_Name" id="Supp_Name" class="form-control" required
                        value="{{ old('Supp_Name') }}">
                </div>

                <div class="mb-3">
                    <label for="Company_Name" class="form-label">Company Name</label>
                    <input type="text" name="Company_Name" id="Company_Name" class="form-control"
                        value="{{ old('Company_Name') }}">
                </div>

                <div class="mb-3">
                    <label for="Phone" class="form-label">Phone</label>
                    <input type="text" name="Phone" id="Phone" class="form-control"
                        value="{{ old('Phone') }}">
                </div>

                <div class="mb-3">
                    <label for="Address1" class="form-label">Address</label>
                    <input type="text" name="Address1" id="Address1" class="form-control"
                        value="{{ old('Address1') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="Email" class="form-label">Email</label>
                    <input type="email" name="Email" id="Email" class="form-control"
                        value="{{ old('Email') }}">
                </div>

                <div class="mb-3">
                    <label for="Web" class="form-label">Website</label>
                    <input type="url" name="Web" id="Web" class="form-control"
                        value="{{ old('Web') }}">
                </div>
                <div class="mb-3">
                    <label for="Fax" class="form-label">Fax</label>
                    <input type="text" name="Fax" id="Fax" class="form-control"
                        value="{{ old('Fax') }}">
                </div>

                <div class="mb-3">
                    <label for="Supp_Group_Name" class="form-label">Supplier Group</label>
                    <select name="Supp_Group_Name" id="Supp_Group_Name" class="form-control">
                        <option value="">Select Group</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->name }}"
                                {{ old('Supp_Group_Name') == $group->name ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="Remark" class="form-label">Remark</label>
            <textarea name="Remark" id="Remark" class="form-control">{{ old('Remark') }}</textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success mr-1">Add Supplier</button>
            <a href="{{ route('suppliers') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</x-layout>
