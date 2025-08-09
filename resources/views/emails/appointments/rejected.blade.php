<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Rejected</title>
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
            background-color: #dc3545;
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
            border-left: 4px solid #dc3545;
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
            background-color: #dc3545;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .reason-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
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
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .alternative-box {
            background-color: #d1ecf1;
            border-left: 4px solid #bee5eb;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Appointment Request Update</h1>
        <p>{{ $appointment->appointment_no }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $appointment->customer->name }},</p>

        <p>We regret to inform you that we are unable to accommodate your appointment request at the requested time.</p>

        <div class="appointment-details">
            <h3>Requested Appointment Details</h3>

            <div class="detail-row">
                <span class="detail-label">Appointment Number:</span>
                <span class="detail-value">{{ $appointment->appointment_no }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Requested Date & Time:</span>
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
                <span class="detail-value"><span class="status-badge">Rejected</span></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Handled by:</span>
                <span class="detail-value">{{ $appointment->handled_by }}</span>
            </div>
        </div>

        @if($appointment->staff_notes)
        <div class="reason-box">
            <h4 style="color: #721c24; margin-top: 0;">Reason for Rejection:</h4>
            <p style="margin-bottom: 0;">{{ $appointment->staff_notes }}</p>
        </div>
        @endif

        <div class="alternative-box">
            <h4 style="margin-top: 0;">What you can do next:</h4>
            <ul>
                <li>Book a new appointment with different date and time</li>
                <li>Contact us directly to discuss alternative options</li>
                <li>Check for available time slots on our booking system</li>
            </ul>
        </div>

        <h4>Contact Information:</h4>
        <p>
            <strong>Phone:</strong> [Your phone number]<br>
            <strong>Email:</strong> [Your email address]<br>
            <strong>Service Hours:</strong> Monday - Friday, 8:30 AM - 4:30 PM
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/customer/appointments/create" class="btn">Book New Appointment</a>
        </div>

        <p>We apologize for any inconvenience caused and appreciate your understanding. We look forward to serving you at a more convenient time.</p>
    </div>

    <div class="footer">
        <p><strong>SMS Auto Service</strong></p>
        <p>Thank you for choosing our service!</p>
        <p><small>This is an automated email. Please do not reply to this message.</small></p>
    </div>
</body>
</html>
