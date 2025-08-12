<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enter Login OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body{background:#f8f9fa}.container{max-width:450px;margin:100px auto} </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Enter Login OTP</h4>
                @if (session('status') === 'otp-sent')
                    <div class="alert alert-success">We sent an OTP to your email.</div>
                @endif
                <form method="POST" action="{{ route('customer.login.otp.verify') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $email }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OTP</label>
                        <input type="text" name="otp" class="form-control @error('otp') is-invalid @enderror" placeholder="6-digit code" required>
                        @error('otp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <hr>
                <a class="btn btn-link" href="{{ route('customer.login.otp.request') }}">Resend OTP</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


