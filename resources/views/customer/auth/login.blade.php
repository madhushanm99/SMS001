<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <h2>Customer Login</h2>
            </div>

            <ul class="nav nav-tabs" id="loginTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab" aria-controls="password-pane" aria-selected="true">Password</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="otp-tab" data-bs-toggle="tab" data-bs-target="#otp-pane" type="button" role="tab" aria-controls="otp-pane" aria-selected="false">Email OTP</button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="loginTabsContent">
                <div class="tab-pane fade show active" id="password-pane" role="tabpanel" aria-labelledby="password-tab">
                    <form method="POST" action="{{ route('customer.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="otp-pane" role="tabpanel" aria-labelledby="otp-tab">
                    @if (session('status') === 'otp-sent')
                        <div class="alert alert-success">OTP sent. Check your email.</div>
                    @endif
                    <form method="POST" action="{{ route('customer.login.otp.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="otp_email" class="form-label">Email Address</label>
                            <input id="otp_email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-secondary">Send OTP</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p>Don't have an account? <a href="{{ route('customer.register') }}">Register here</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const params = new URLSearchParams(window.location.search);
            const email = params.get('email');
            const temp = params.get('temp');
            if (email) {
                const emailInput = document.getElementById('email');
                const otpEmailInput = document.getElementById('otp_email');
                if (emailInput) emailInput.value = email;
                if (otpEmailInput) otpEmailInput.value = email;
            }
            if (temp) {
                const pwInput = document.getElementById('password');
                if (pwInput) pwInput.value = temp;
            }
        })();
    </script>
</body>
</html>
