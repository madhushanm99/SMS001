<x-customer.layouts.app :title="'Book New Appointment'">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h2">Book New Appointment</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('customer.appointments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Appointments
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment Booking Form</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('customer.appointments.store') }}" id="appointmentForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_no" class="form-label">Select Vehicle <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_no') is-invalid @enderror"
                                id="vehicle_no"
                                name="vehicle_no"
                                required>
                            <option value="">Choose your vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->vehicle_no }}" {{ old('vehicle_no') == $vehicle->vehicle_no ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_no }} - {{ $vehicle->brand->name ?? 'Unknown' }} {{ $vehicle->model }} ({{ $vehicle->year_of_manufacture }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_type') is-invalid @enderror"
                                id="service_type"
                                name="service_type"
                                required>
                            <option value="">Select service type</option>
                            <option value="NS" {{ old('service_type') == 'NS' ? 'selected' : '' }}>Normal Service</option>
                            <option value="FS" {{ old('service_type') == 'FS' ? 'selected' : '' }}>Full Service</option>
                        </select>
                        @error('service_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Normal Service:</strong> Basic maintenance, oil change, filters<br>
                            <strong>Full Service:</strong> Comprehensive inspection and maintenance
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="appointment_date" class="form-label">Preferred Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('appointment_date') is-invalid @enderror"
                               id="appointment_date"
                               name="appointment_date"
                               value="{{ old('appointment_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               required>
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Appointments can be booked from tomorrow onwards</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="appointment_time" class="form-label">Preferred Time <span class="text-danger">*</span></label>
                        <select class="form-select @error('appointment_time') is-invalid @enderror"
                                id="appointment_time"
                                name="appointment_time"
                                required
                                disabled>
                            <option value="">Select date first</option>
                        </select>
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Available slots: 8:30 AM - 11:30 AM, 12:30 PM - 3:30 PM
                        </div>
                        <div id="time-slot-loading" class="d-none">
                            <small class="text-info"><i class="bi bi-hourglass-split"></i> Loading available time slots...</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="customer_notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control @error('customer_notes') is-invalid @enderror"
                                  id="customer_notes"
                                  name="customer_notes"
                                  rows="4"
                                  placeholder="Any specific concerns, requests, or information about your vehicle...">{{ old('customer_notes') }}</textarea>
                        @error('customer_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Maximum 1000 characters</div>
                    </div>
                </div>

                <!-- Appointment Summary -->
                <div class="card bg-light mb-3" id="appointment-summary" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">Appointment Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Vehicle:</strong> <span id="summary-vehicle">-</span><br>
                                <strong>Service Type:</strong> <span id="summary-service">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Date:</strong> <span id="summary-date">-</span><br>
                                <strong>Time:</strong> <span id="summary-time">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="bi bi-calendar-check"></i> Book Appointment
                        </button>
                        <a href="{{ route('customer.appointments.index') }}" class="btn btn-secondary ms-2">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentDateInput = document.getElementById('appointment_date');
            const appointmentTimeSelect = document.getElementById('appointment_time');
            const timeSlotLoading = document.getElementById('time-slot-loading');
            const appointmentSummary = document.getElementById('appointment-summary');

            // Form elements for summary
            const vehicleSelect = document.getElementById('vehicle_no');
            const serviceSelect = document.getElementById('service_type');
            const summaryVehicle = document.getElementById('summary-vehicle');
            const summaryService = document.getElementById('summary-service');
            const summaryDate = document.getElementById('summary-date');
            const summaryTime = document.getElementById('summary-time');

            // Load time slots when date changes
            appointmentDateInput.addEventListener('change', function() {
                const selectedDate = this.value;

                if (selectedDate) {
                    loadTimeSlots(selectedDate);
                } else {
                    appointmentTimeSelect.disabled = true;
                    appointmentTimeSelect.innerHTML = '<option value="">Select date first</option>';
                }
                updateSummary();
            });

            // Update summary when other fields change
            vehicleSelect.addEventListener('change', updateSummary);
            serviceSelect.addEventListener('change', updateSummary);
            appointmentTimeSelect.addEventListener('change', updateSummary);

            function loadTimeSlots(date) {
                timeSlotLoading.classList.remove('d-none');
                appointmentTimeSelect.disabled = true;
                appointmentTimeSelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`{{ route('customer.appointments.available-slots') }}?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        timeSlotLoading.classList.add('d-none');
                        appointmentTimeSelect.disabled = false;

                        if (data.success) {
                            appointmentTimeSelect.innerHTML = '<option value="">Select time slot</option>';

                            if (Object.keys(data.slots).length === 0) {
                                appointmentTimeSelect.innerHTML = '<option value="">No available slots for this date</option>';
                                appointmentTimeSelect.disabled = true;
                            } else {
                                Object.entries(data.slots).forEach(([time, label]) => {
                                    const option = document.createElement('option');
                                    option.value = time;
                                    option.textContent = label;
                                    appointmentTimeSelect.appendChild(option);
                                });
                            }
                        } else {
                            appointmentTimeSelect.innerHTML = '<option value="">' + data.message + '</option>';
                            appointmentTimeSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading time slots:', error);
                        timeSlotLoading.classList.add('d-none');
                        appointmentTimeSelect.disabled = false;
                        appointmentTimeSelect.innerHTML = '<option value="">Error loading time slots</option>';
                    });
            }

            function updateSummary() {
                const vehicle = vehicleSelect.options[vehicleSelect.selectedIndex]?.text || '-';
                const service = serviceSelect.options[serviceSelect.selectedIndex]?.text || '-';
                const date = appointmentDateInput.value ? new Date(appointmentDateInput.value).toLocaleDateString() : '-';
                const time = appointmentTimeSelect.options[appointmentTimeSelect.selectedIndex]?.text || '-';

                summaryVehicle.textContent = vehicle !== 'Choose your vehicle' ? vehicle : '-';
                summaryService.textContent = service !== 'Select service type' ? service : '-';
                summaryDate.textContent = date;
                summaryTime.textContent = time !== 'Select time slot' && time !== 'Select date first' ? time : '-';

                // Show summary if all fields are filled
                const allFilled = vehicleSelect.value && serviceSelect.value && appointmentDateInput.value && appointmentTimeSelect.value;
                appointmentSummary.style.display = allFilled ? 'block' : 'none';
            }

            // Prevent weekend selection (optional)
            appointmentDateInput.addEventListener('input', function() {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay();

                if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday = 0, Saturday = 6
                    alert('Appointments are not available on weekends. Please select a weekday.');
                    this.value = '';
                }
            });
        });
    </script>
    @endpush
</x-customer.layouts.app>
