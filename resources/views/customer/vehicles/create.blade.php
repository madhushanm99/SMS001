<x-customer.layouts.app :title="'Register New Vehicle'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Register New Vehicle</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Vehicles
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Vehicle Registration Form</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('customer.vehicles.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_no" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('vehicle_no') is-invalid @enderror"
                               id="vehicle_no"
                               name="vehicle_no"
                               value="{{ old('vehicle_no') }}"
                               placeholder="e.g., ABC-1234"
                               required>
                        <div class="form-text">Format: ABC-1234 or AB-1234</div>
                        @error('vehicle_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="vehicle_no_feedback" class="form-text"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="brand_id" class="form-label">Vehicle Brand <span class="text-danger">*</span></label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id"
                                name="brand_id"
                                required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('model') is-invalid @enderror"
                               id="model"
                               name="model"
                               value="{{ old('model') }}"
                               placeholder="e.g., Corolla, Civic"
                               required>
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="year_of_manufacture" class="form-label">Year of Manufacture <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control @error('year_of_manufacture') is-invalid @enderror"
                               id="year_of_manufacture"
                               name="year_of_manufacture"
                               value="{{ old('year_of_manufacture') }}"
                               min="1900"
                               max="{{ date('Y') + 1 }}"
                               required>
                        @error('year_of_manufacture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="engine_no" class="form-label">Engine Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('engine_no') is-invalid @enderror"
                               id="engine_no"
                               name="engine_no"
                               value="{{ old('engine_no') }}"
                               placeholder="Engine Number"
                               required>
                        @error('engine_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="chassis_no" class="form-label">Chassis Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('chassis_no') is-invalid @enderror"
                               id="chassis_no"
                               name="chassis_no"
                               value="{{ old('chassis_no') }}"
                               placeholder="Chassis Number"
                               required>
                        @error('chassis_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="route_id" class="form-label">Route <span class="text-danger">*</span></label>
                        <select class="form-select @error('route_id') is-invalid @enderror"
                                id="route_id"
                                name="route_id"
                                required>
                            <option value="">Select Route</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="date_of_purchase" class="form-label">Date of Purchase <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('date_of_purchase') is-invalid @enderror"
                               id="date_of_purchase"
                               name="date_of_purchase"
                               value="{{ old('date_of_purchase') }}"
                               max="{{ date('Y-m-d') }}"
                               required>
                        @error('date_of_purchase')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="registration_status"
                                   name="registration_status"
                                   value="1"
                                   {{ old('registration_status') ? 'checked' : '' }}>
                            <label class="form-check-label" for="registration_status">
                                Vehicle is registered with DMT
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Register Vehicle
                        </button>
                        <a href="{{ route('customer.vehicles.index') }}" class="btn btn-secondary ms-2">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('vehicle_no').addEventListener('input', function() {
            const vehicleNo = this.value.toUpperCase();
            const feedback = document.getElementById('vehicle_no_feedback');

            if (vehicleNo.length >= 6) {
                // Check availability via AJAX
                fetch(`{{ route('customer.vehicles.check-availability') }}?vehicle_no=${vehicleNo}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            feedback.className = 'form-text text-success';
                            feedback.textContent = '✓ Vehicle number is available';
                        } else {
                            feedback.className = 'form-text text-danger';
                            feedback.textContent = '✗ Vehicle number is already registered';
                        }
                    })
                    .catch(error => {
                        feedback.className = 'form-text';
                        feedback.textContent = '';
                    });
            } else {
                feedback.className = 'form-text';
                feedback.textContent = '';
            }

            // Auto format vehicle number
            this.value = vehicleNo;
        });
    </script>
    @endpush
</x-customer.layouts.app>
