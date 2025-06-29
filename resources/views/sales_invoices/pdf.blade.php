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

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer generated invoice.</p>
    </div>
</body>
</html> 