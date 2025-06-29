<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_no }}</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .invoice-details {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .customer-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        ul {
            padding-left: 0;
            list-style: none;
        }
        li {
            margin-bottom: 5px;
        }
        strong {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #495057;">{{ config('app.name') }}</h1>
        <p style="margin: 5px 0 0 0; color: #6c757d;">Invoice Notification</p>
    </div>

    <div class="content">
        <h2 style="color: #495057;">Invoice {{ $invoice->invoice_no }}</h2>
        
        <p>Dear {{ $customer->name }},</p>
        
        <p>Thank you for your business! Please find attached your invoice details below:</p>

        <div class="invoice-details">
            <h4 style="margin-top: 0; color: #495057;">Invoice Information</h4>
            <ul>
                <li><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</li>
                <li><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') }}</li>
                <li><strong>Grand Total:</strong> Rs. {{ number_format($invoice->grand_total, 2) }}</li>
            </ul>
        </div>

        <div class="customer-details">
            <h4 style="margin-top: 0; color: #495057;">Customer Information</h4>
            <ul>
                <li><strong>Name:</strong> {{ $customer->name }}</li>
                <li><strong>Phone:</strong> {{ $customer->phone }}</li>
                @if($customer->email)
                <li><strong>Email:</strong> {{ $customer->email }}</li>
                @endif
                @if($customer->address)
                <li><strong>Address:</strong> {{ $customer->address }}</li>
                @endif
            </ul>
        </div>

        <div style="background-color: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h4 style="margin-top: 0; color: #0c5460;">Invoice Summary</h4>
            <p style="margin-bottom: 0;">The invoice contains <strong>{{ $invoice->items->count() }} item(s)</strong> with a grand total of <strong>Rs. {{ number_format($invoice->grand_total, 2) }}</strong>.</p>
        </div>

        <p>Please find the detailed invoice attached as a PDF document. You can download, print, or save this invoice for your records.</p>

        <p>If you have any questions about this invoice, please don't hesitate to contact us.</p>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}" class="btn">Visit Our Website</a>
        </div>

        <p>Thank you for choosing {{ config('app.name') }}!</p>

        <p style="margin-bottom: 0;">
            Best regards,<br>
            <strong>{{ config('app.name') }} Team</strong>
        </p>
    </div>

    <div class="footer">
        <p style="margin: 0;">This is an automated email. Please do not reply to this email address.</p>
        <p style="margin: 5px 0 0 0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html> 