<x-layout> <x-slot name="title">Add New Customer</x-slot>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('customers.store') }}" id="customer_form"> @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-4">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-4">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>NIC</label>
                <input type="text" name="nic" class="form-control" value="{{ old('nic') }}">
            </div>
            <div class="col-md-4">
                <label>Group</label>
                <input type="text" name="group_name" class="form-control" value="All Groups" >
            </div>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" value="{{ old('address') }}"></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-success">Save Customer</button>
        </div>
    </form>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('customer_form');
                const fields = {
                    name: {
                        required: true,
                        validator: val => /^[A-Za-z\s]{2,}$/.test(val),
                        message: 'Name must contain only letters and spaces.'
                    },
                    phone: {
                        required: false,
                        validator: val => val === '' || /^0\d{9}$/.test(val),
                        message: 'Enter a valid mobile number (e.g., 0712345678).'
                    },
                    email: {
                        required: false,
                        validator: val => val === '' || /^[^\s@]+@[^\s@]+.[^\s@]+$/.test(val),
                        message: 'Enter a valid email address.'
                    },
                    nic: {
                        required: false,
                        validator: val => val === '' || /^(\d{9}[VXvx]|\d{12})$/.test(val),
                        message: 'Enter a valid NIC (9 digits + V/X or 12 digits).'
                    }
                };
                Object.keys(fields).forEach(field => {
                    const input = form.elements[field];
                    if (!input) return;
                    input.addEventListener('blur', () => validateField(field));
                    input.addEventListener('input', () => clearError(field));
                });
                form.addEventListener('submit', e => {
                    let hasErrors = false;
                    Object.keys(fields).forEach(field => {
                        if (!validateField(field)) hasErrors = true;
                    });
                    if (hasErrors) e.preventDefault();
                });

                function validateField(field) {
                    const input = form.elements[field];
                    const errorId = field + '_error';
                    const value = input.value.trim();
                    const {
                        required,
                        validator,
                        message
                    } = fields[field];
                    if ((required && value === '') || (value && !validator(value))) {
                        if (!document.getElementById(errorId)) {
                            const small = document.createElement('small');
                            small.id = errorId;
                            small.className = 'text-danger';
                            small.textContent = message;
                            input.classList.add('is-invalid');
                            input.insertAdjacentElement('afterend', small);
                        }
                        return false;
                    } else {
                        clearError(field);
                        return true;
                    }
                }

                function clearError(field) {
                    const input = form.elements[field];
                    const error = document.getElementById(field + '_error');
                    if (error) error.remove();
                    input.classList.remove('is-invalid');
                }
            });
        </script>
    @endpush
</x-layout>
