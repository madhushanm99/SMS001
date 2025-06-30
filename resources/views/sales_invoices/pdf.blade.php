<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-right {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div>Address Line 1, Address Line 2</div>
        <div>Phone: +94 XX XXX XXXX | Email: info@company.com</div>
        <div class="invoice-title">SALES INVOICE</div>
    </div>

    <div class="info-section">
        <div class="info-left">
            <strong>Bill To:</strong><br>
            {{ $invoice->customer->name }}<br>
            @if($invoice->customer->address)
                {{ $invoice->customer->address }}<br>
            @endif
            Phone: {{ $invoice->customer->phone }}<br>
            @if($invoice->customer->email)
                Email: {{ $invoice->customer->email }}
            @endif
        </div>
        <div class="info-right">
            <strong>Invoice #:</strong> {{ $invoice->invoice_no }}<br>
            <strong>Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
            <strong>Status:</strong> {{ ucfirst($invoice->status) }}<br>
            <strong>Created By:</strong> {{ $invoice->created_by }}
        </div>
    </div>

    @if($invoice->notes)
        <div style="margin-bottom: 20px;">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Discount</th>
                <th style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td class="text-center">{{ $item->line_no }}</td>
                    <td>
                        <strong>{{ $item->item_name }}</strong><br>
                        <small>{{ $item->item_id }}</small>
                    </td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-center">{{ $item->discount }}%</td>
                    <td class="text-right">Rs. {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>Rs. {{ number_format($invoice->grand_total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- Payment Status Section --}}
    @php
        $totalPaid = $invoice->paymentTransactions()
            ->where('status', 'completed')
            ->where('type', 'cash_in')
            ->sum('amount');
        $outstandingAmount = $invoice->grand_total - $totalPaid;
        $paymentStatus = $totalPaid >= $invoice->grand_total ? 'Paid' : ($totalPaid > 0 ? 'Partially Paid' : 'Unpaid');
    @endphp

    <div style="margin: 30px 0; border: 1px solid #ddd; padding: 15px; background-color: #f8f9fa;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <strong>Payment Status:</strong><br>
                <span style="font-size: 14px; font-weight: bold; 
                    color: {{ $paymentStatus === 'Paid' ? '#28a745' : ($paymentStatus === 'Partially Paid' ? '#ffc107' : '#dc3545') }};">
                    {{ $paymentStatus }}
                </span>
            </div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: top;">
                <strong>Amount Paid:</strong> Rs. {{ number_format($totalPaid, 2) }}<br>
                @if($outstandingAmount > 0)
                    <strong style="color: #dc3545;">Outstanding Balance:</strong> 
                    <span style="color: #dc3545; font-weight: bold;">Rs. {{ number_format($outstandingAmount, 2) }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Details --}}
    @if($totalPaid > 0)
        <div style="margin-bottom: 30px;">
            <h4 style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Payment History</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Date</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Method</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Reference</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->paymentTransactions()->where('status', 'completed')->where('type', 'cash_in')->get() as $payment)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 6px;">
                                {{ $payment->transaction_date->format('M d, Y') }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 6px;">
                                {{ $payment->paymentMethod->name ?? 'N/A' }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 6px;">
                                {{ $payment->reference_no ?? '-' }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer generated invoice.</p>
    </div>
</body>
</html> 