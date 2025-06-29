<x-layout> <x-slot name="title">Edit Vehicle - {{ $vehicle->vehicle_no }}</x-slot>
    <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" id="vehicle_form"> @csrf @method('PUT')
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control" disabled>
                <option value="{{ $vehicle->customer->id }}">
                    {{ $vehicle->customer->name }} ({{ $vehicle->customer->phone }})
                </option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Vehicle No *</label>
                <input type="text" name="vehicle_no" class="form-control" required
                    value="{{ $vehicle->vehicle_no }}">
            </div>
            <div class="col-md-4">
                <label>Brand</label>
                <select name="brand_id" class="form-control" required>
                    <option value="">Select brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected($brand->id == $vehicle->brand_id)>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Model</label>
                <input type="text" name="model" class="form-control" required value="{{ $vehicle->model }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Engine No</label>
                <input type="text" name="engine_no" class="form-control" required value="{{ $vehicle->engine_no }}">
            </div>
            <div class="col-md-4">
                <label>Chassis No</label>
                <input type="text" name="chassis_no" class="form-control" required
                    value="{{ $vehicle->chassis_no }}">
            </div>
            <div class="col-md-4">
                <label>Route</label>
                <select name="route_id" class="form-control" required>
                    <option value="">Select route</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" @selected($route->id == $vehicle->route_id)>
                            {{ $route->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Year of Manufacture</label>
                <input type="number" name="year_of_manufacture" class="form-control" required
                    value="{{ $vehicle->year_of_manufacture }}">
            </div>
            <div class="col-md-4">
                <label>Date of Purchase</label>
                <input type="date" name="date_of_purchase" class="form-control" required
                    value="{{ $vehicle->date_of_purchase }}">
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="registration_status" class="form-check-input" value="1"
                {{ $vehicle->registration_status ? 'checked' : '' }}>
            <label class="form-check-label">This record is complete (staff entry)</label>
        </div>

        <button type="submit" class="btn btn-success">Update Vehicle</button>
    </form>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('vehicle_form');
                const fields = {
                    vehicle_no: {
                        required: true,
                        validator: val => /^[A-Z]{2,3}-\d{4}$/.test(val),
                        message: 'Use valid SL motorcycle plate (e.g. AB-1234 or ABC-5678).'
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
                        validator: val => /^[A-Za-z0-9-/]+$/.test(val),
                        message: 'Engine No must be alphanumeric.'
                    },
                    chassis_no: {
                        required: true,
                        validator: val => /^[A-Za-z0-9-/]+$/.test(val),
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
