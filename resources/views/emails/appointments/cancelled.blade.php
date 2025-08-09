<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Cancelled</title>
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
            background-color: #6c757d;
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
            border-left: 4px solid #6c757d;
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
            background-color: #6c757d;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Appointment Cancelled</h1>
        <p>{{ $appointment->appointment_no }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $appointment->customer->name }},</p>

        <p>This email confirms that your appointment has been successfully cancelled.</p>

        <div class="appointment-details">
            <h3>Cancelled Appointment Details</h3>

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
                <span class="detail-value"><span class="status-badge">Cancelled</span></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Cancelled on:</span>
                <span class="detail-value">{{ $appointment->handled_at ? $appointment->handled_at->format('d M Y, h:i A') : 'Recently' }}</span>
            </div>
        </div>

        <div class="info-box">
            <h4 style="color: #0c5460; margin-top: 0;">Important Information:</h4>
            <ul style="margin-bottom: 0;">
                <li>Your appointment time slot is now available for other customers</li>
                <li>No charges have been applied for this cancellation</li>
                <li>You can book a new appointment anytime through our system</li>
                <li>If you cancelled by mistake, please book a new appointment immediately</li>
            </ul>
        </div>

        <h4>Need to book a new appointment?</h4>
        <p>You can easily book a new appointment through our online system. We have various time slots available to suit your schedule.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/customer/appointments/create" class="btn">Book New Appointment</a>
        </div>

        <h4>Contact Us:</h4>
        <p>If you have any questions or need assistance, please don't hesitate to contact us:</p>
        <p>
            <strong>Phone:</strong> [Your phone number]<br>
            <strong>Email:</strong> [Your email address]<br>
            <strong>Service Hours:</strong> Monday - Friday, 8:30 AM - 4:30 PM
        </p>

        <p>We hope to serve you again soon!</p>
    </div>

    <div class="footer">
        <p><strong>SMS Auto Service</strong></p>
        <p>Thank you for choosing our service!</p>
        <p><small>This is an automated email. Please do not reply to this message.</small></p>
    </div>
</body>
</html>
