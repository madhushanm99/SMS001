<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 80px auto; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Verify your email</h4>
                <p class="text-muted">Before continuing, please verify your email address by clicking the link we just emailed to you. If you didn't receive the email, you can request another one below.</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success">A new verification link has been sent to your email address.</div>
                @endif

                <form method="POST" action="{{ route('customer.verification.send') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                </form>

                <a href="{{ route('customer.logout') }}" class="btn btn-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a>
                <form id="logout-form" method="POST" action="{{ route('customer.logout') }}" class="d-none">@csrf</form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


