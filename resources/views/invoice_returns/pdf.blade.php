<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Return - {{ $return->return_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .return-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .return-info > div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-section h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SMS Management System</div>
        <h2>INVOICE RETURN</h2>
        <p>Return #{{ $return->return_no }}</p>
    </div>

    <div class="return-info">
        <div class="info-section">
            <h3>Return Information</h3>
            <p><strong>Return No:</strong> {{ $return->return_no }}</p>
            <p><strong>Return Date:</strong> {{ $return->return_date->format('Y-m-d') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $return->status }}">{{ ucfirst($return->status) }}</span>
            </p>
            <p><strong>Processed By:</strong> {{ $return->processed_by }}</p>
            <p><strong>Reason:</strong> {{ $return->reason }}</p>
        </div>
        
        <div class="info-section">
            <h3>Original Invoice</h3>
            <p><strong>Invoice No:</strong> {{ $return->invoice_no }}</p>
            <p><strong>Invoice Date:</strong> {{ $return->salesInvoice->invoice_date->format('Y-m-d') }}</p>
            <p><strong>Original Amount:</strong> Rs. {{ number_format($return->salesInvoice->grand_total, 2) }}</p>
            <br>
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $return->customer->name }}</p>
            <p><strong>Phone:</strong> {{ $return->customer->phone }}</p>
            @if($return->customer->email)
            <p><strong>Email:</strong> {{ $return->customer->email }}</p>
            @endif
        </div>
    </div>

    @if($return->notes)
    <div class="info-section">
        <h3>Additional Notes</h3>
        <p>{{ $return->notes }}</p>
    </div>
    @endif

    <h3>Returned Items</h3>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty Returned</th>
                <th class="text-center">Original Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-center">Discount</th>
                <th class="text-right">Line Total</th>
                <th>Return Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->item_name }}</strong><br>
                    <small>{{ $item->item_id }}</small>
                </td>
                <td class="text-center">{{ $item->qty_returned }}</td>
                <td class="text-center">{{ $item->original_qty }}</td>
                <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-center">{{ $item->discount }}%</td>
                <td class="text-right">Rs. {{ number_format($item->line_total, 2) }}</td>
                <td><small>{{ $item->return_reason ?: 'Not specified' }}</small></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>Total Return Amount:</strong></td>
                <td class="text-right"><strong>Rs. {{ number_format($return->total_amount, 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>This is a computer-generated document and does not require a signature.</p>
    </div>
</body>
</html> 