<x-layout> <x-slot name="title">Register Vehicle</x-slot>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('vehicles.store') }}"> @csrf

        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" id="customer_select" class="form-control"></select>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Vehicle No *</label>
                <input type="text" name="vehicle_no" class="form-control" placeholder="e.g. AB-1234 or ABK-5678" required>
            </div>
            <div class="col-md-4">
                <label>Brand</label>
                <select name="brand_id" class="form-control">
                    <option value="">Select brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Model</label>
                <input type="text" name="model" class="form-control">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Engine No</label>
                <input type="text" name="engine_no" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Chassis No</label>
                <input type="text" name="chassis_no" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Route</label>
                <select name="route_id" class="form-control">
                    <option value="">Select route</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Year of Manufacture</label>
                <input type="number" name="year_of_manufacture" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Date of Purchase</label>
                <input type="date" name="date_of_purchase" class="form-control">
            </div>
        </div>

        <input type="hidden" name="registration_status" value="1">


        <button type="submit" class="btn btn-success">Save</button>
    </form>
    @push('scripts')
        <script>
            $('#customer_select').select2({
                placeholder: 'Search customer by name / phone / NIC',
                ajax: {
                    url: '{{ route('customers.search') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('form');
                const fields = {
                    customer_id: {
                        required: true,
                        message: 'Customer is required.'
                    },
                    vehicle_no: {
                        required: true,
                        validator: val => /^[A-Z]{2,3}-\d{4}$/.test(val),
                        message: 'Use valid Vehicle Re No (e.g. AB-1234 or ABK-5678).'
                    },
                    brand_id: {
                        required: true,
                        message: 'Brand is required.'
                    },
                    model: {
                        required: true,
                        message: 'Model is required.'
                    },
                    engine_no: {
                        required: true,
                        validator: val => /^[A-Za-z0-9\-\/]+$/.test(val),
                        message: 'Engine No must be alphanumeric.'
                    },
                    chassis_no: {
                        required: true,
                        validator: val => /^[A-Za-z0-9\-\/]+$/.test(val),
                        message: 'Chassis No must be alphanumeric.'
                    },
                    route_id: {
                        required: true,
                        message: 'Route is required.'
                    },
                    year_of_manufacture: {
                        required: true,
                        validator: val => /^\d{4}$/.test(val),
                        message: 'Enter valid year (e.g. 2020).'
                    },
                    date_of_purchase: {
                        required: true,
                        message: 'Date of Purchase is required.'
                    }
                };
                Object.keys(fields).forEach(field => {
                    const input = form.elements[field];
                    if (!input) return;
                    input.addEventListener('blur', () => validate(field));
                    input.addEventListener('input', () => clearError(field));
                });
                form.addEventListener('submit', e => {
                    let invalid = false;
                    Object.keys(fields).forEach(field => {
                        if (!validate(field)) invalid = true;
                    });
                    if (invalid) e.preventDefault();
                });

                function validate(field) {
                    const input = form.elements[field];
                    const value = (input.value || '').trim();
                    const errorId = `${field}_error`;
                    const config = fields[field];
                    if ((config.required && value === '') || (config.validator && !config.validator(value))) {
                        if (!document.getElementById(errorId)) {
                            const small = document.createElement('small');
                            small.id = errorId;
                            small.className = 'text-danger';
                            small.textContent = config.message;
                            input.classList.add('is-invalid');
                            input.insertAdjacentElement('afterend', small);
                        }
                        return false;
                    }
                    clearError(field);
                    return true;
                }

                function clearError(field) {
                    const input = form.elements[field];
                    const error = document.getElementById(`${field}_error`);
                    if (error) error.remove();
                    input.classList.remove('is-invalid');
                }
            });
        </script>
    @endpush
</x-layout>
