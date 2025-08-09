<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmed</title>
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
            background-color: #28a745;
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
            border-left: 4px solid #28a745;
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
            background-color: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .highlight-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
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
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .checklist {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Appointment Confirmed!</h1>
        <p>{{ $appointment->appointment_no }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $appointment->customer->name }},</p>

        <div class="highlight-box">
            <h3 style="color: #28a745; margin: 0;">Great news! Your appointment has been confirmed.</h3>
            <p style="margin: 10px 0 0 0;">Please save this email for your records.</p>
        </div>

        <div class="appointment-details">
            <h3>Confirmed Appointment Details</h3>

            <div class="detail-row">
                <span class="detail-label">Appointment Number:</span>
                <span class="detail-value">{{ $appointment->appointment_no }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date & Time:</span>
                <span class="detail-value"><strong>{{ $appointment->getFormattedDateTime() }}</strong></span>
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
                <span class="detail-value"><span class="status-badge">Confirmed</span></span>
            </div>

            @if($appointment->staff_notes)
            <div class="detail-row">
                <span class="detail-label">Staff Notes:</span>
                <span class="detail-value">{{ $appointment->staff_notes }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Confirmed by:</span>
                <span class="detail-value">{{ $appointment->handled_by }}</span>
            </div>
        </div>

        <div class="checklist">
            <h4>Before Your Appointment:</h4>
            <ul style="text-align: left;">
                <li>Arrive 15 minutes before your scheduled time</li>
                <li>Bring your vehicle registration documents</li>
                <li>Remove any personal items from your vehicle</li>
                <li>Note any specific issues or concerns about your vehicle</li>
                @if($appointment->service_type === 'FS')
                <li>For full service: Allow extra time as comprehensive service may take longer</li>
                @endif
            </ul>
        </div>

        <h4>Service Center Information:</h4>
        <p>
            <strong>Location:</strong> SMS Auto Service Center<br>
            <strong>Contact:</strong> [Your phone number]<br>
            <strong>Address:</strong> [Your service center address]
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/customer/appointments/{{ $appointment->id }}" class="btn">View Full Details</a>
        </div>

        <p><strong>Need to reschedule?</strong> Please contact us at least 24 hours before your appointment time.</p>
    </div>

    <div class="footer">
        <p><strong>SMS Auto Service</strong></p>
        <p>We look forward to serving you!</p>
        <p><small>This is an automated email. Please do not reply to this message.</small></p>
    </div>
</body>
</html>
