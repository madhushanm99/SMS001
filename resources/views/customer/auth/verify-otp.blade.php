<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email by OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body{background:#f8f9fa}.container{max-width:500px;margin:80px auto} </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Verify your email</h4>
                @if (session('status') === 'otp-sent')
                    <div class="alert alert-success">We have emailed you an OTP.</div>
                @endif
                <form method="POST" action="{{ route('customer.verification.otp.verify') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Enter OTP</label>
                        <input type="text" name="otp" class="form-control @error('otp') is-invalid @enderror" placeholder="6-digit code" required>
                        @error('otp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>
                <hr>
                <form method="POST" action="{{ route('customer.verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-link">Resend OTP</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


