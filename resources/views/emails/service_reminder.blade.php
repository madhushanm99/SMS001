<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Reminder</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 600px; margin: 0 auto; padding: 16px; }
        .btn { display: inline-block; padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 4px; }
        .muted { color: #666; font-size: 12px; }
    </style>
    </head>
<body>
<div class="container">
    <p>Hi {{ $customerName }},</p>
    <p>This is a friendly reminder that your vehicle <strong>{{ $vehicleNo }}</strong> is due for service{{ $nextServiceDate ? ' around ' . \Carbon\Carbon::parse($nextServiceDate)->format('M d, Y') : '' }}.</p>
    <p>We recommend booking your appointment in advance to avoid delays.</p>
    <p>
        <a class="btn" href="{{ url('/') }}">Book Appointment</a>
    </p>
    <p>If you have already completed the service, please ignore this email.</p>
    <p class="muted">Thank you,<br>{{ config('app.name') }}</p>
</div>
</body>
</html>


