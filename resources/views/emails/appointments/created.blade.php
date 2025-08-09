<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Request Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .appointment-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4e73df;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #ffc107;
            color: #856404;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Appointment Request Submitted</h1>
        <p>{{ $appointment->appointment_no }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $appointment->customer->name }},</p>

        <p>Thank you for submitting your appointment request. We have received your request and our team will review it shortly.</p>

        <div class="appointment-details">
            <h3>Appointment Details</h3>

            <div class="detail-row">
                <span class="detail-label">Appointment Number:</span>
                <span class="detail-value">{{ $appointment->appointment_no }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date & Time:</span>
                <span class="detail-value">{{ $appointment->getFormattedDateTime() }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Vehicle:</span>
                <span class="detail-value">
                    {{ $appointment->vehicle_no }}
                    @if($appointment->vehicle)
                        ({{ $appointment->vehicle->brand->name ?? '' }} {{ $appointment->vehicle->model }})
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Service Type:</span>
                <span class="detail-value">{{ $appointment->getServiceTypeLabel() }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value"><span class="status-badge">Pending Approval</span></span>
            </div>

            @if($appointment->customer_notes)
            <div class="detail-row">
                <span class="detail-label">Your Notes:</span>
                <span class="detail-value">{{ $appointment->customer_notes }}</span>
            </div>
            @endif
        </div>

        <h4>What happens next?</h4>
        <ul>
            <li>Our team will review your appointment request</li>
            <li>You will receive a confirmation email once approved</li>
            <li>If we need to reschedule, we will contact you directly</li>
            <li>Please arrive 15 minutes before your scheduled time</li>
        </ul>

        <p><strong>Important:</strong> This appointment is not yet confirmed. Please wait for our confirmation email before visiting our service center.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/customer/appointments/{{ $appointment->id }}" class="btn">View Appointment Details</a>
        </div>

        <p>If you need to cancel or modify this appointment, please contact us as soon as possible.</p>
    </div>

    <div class="footer">
        <p><strong>SMS Auto Service</strong></p>
        <p>Thank you for choosing our service!</p>
        <p><small>This is an automated email. Please do not reply to this message.</small></p>
    </div>
</body>
</html>
