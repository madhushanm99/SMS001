<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Invoice {{ $serviceInvoice->invoice_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
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
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 15px;
        }
        
        .invoice-info {
            width: 100%;
            margin-bottom: 25px;
        }
        
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-info td {
            padding: 8px;
            vertical-align: top;
        }
        
        .label {
            font-weight: bold;
            color: #2c3e50;
            width: 120px;
        }
        
        .customer-info, .invoice-details {
            width: 48%;
            float: left;
        }
        
        .customer-info {
            margin-right: 4%;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            clear: both;
        }
        
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 11px;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .job-section, .parts-section {
            margin-bottom: 25px;
        }
        
        .subsection-title {
            font-size: 12px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #3498db;
        }
        
        .subtotal-row {
            background-color: #e8f4f8 !important;
            font-weight: bold;
        }
        
        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .total-table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
        }
        
        .total-table .label-col {
            background-color: #ecf0f1;
            font-weight: bold;
            width: 60%;
        }
        
        .total-table .amount-col {
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            background-color: #27ae60 !important;
            color: white;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 50px;
            clear: both;
            border-top: 1px solid #bdc3c7;
            padding-top: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .notes {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            clear: both;
        }
        
        .notes-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #27ae60;
            color: white;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-hold {
            background-color: #f39c12;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div class="company-details">
            Address Line 1, Address Line 2<br>
            Phone: +94 XX XXX XXXX | Email: info@company.com
        </div>
        <div class="invoice-title">SERVICE INVOICE</div>
    </div>

    <!-- Invoice and Customer Information -->
    <div class="invoice-info">
        <div class="invoice-details">
            <div class="section-title">Invoice Details</div>
            <table>
                <tr>
                    <td class="label">Invoice No:</td>
                    <td><strong>{{ $serviceInvoice->invoice_no }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice Date:</td>
                    <td>{{ $serviceInvoice->invoice_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td>
                        <span class="status-badge {{ $serviceInvoice->status === 'hold' ? 'status-hold' : '' }}">
                            {{ ucfirst($serviceInvoice->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Created By:</td>
                    <td>{{ $serviceInvoice->created_by }}</td>
                </tr>
            </table>
        </div>

        <div class="customer-info">
            <div class="section-title">Customer Information</div>
            <table>
                <tr>
                    <td class="label">Customer:</td>
                    <td><strong>{{ $serviceInvoice->customer->name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Customer ID:</td>
                    <td>{{ $serviceInvoice->customer_id }}</td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $serviceInvoice->customer->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Vehicle No:</td>
                    <td><strong>{{ $serviceInvoice->vehicle_no ?? 'N/A' }}</strong></td>
                </tr>
                @if($serviceInvoice->mileage)
                <tr>
                    <td class="label">Mileage:</td>
                    <td>{{ number_format($serviceInvoice->mileage) }} km</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Job Types Section -->
    @if($serviceInvoice->jobItems->count() > 0)
    <div class="job-section">
        <div class="subsection-title">Job Types</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">Job Type</th>
                    <th style="width: 15%">Quantity</th>
                    <th style="width: 20%">Unit Price</th>
                    <th style="width: 20%">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceInvoice->jobItems as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3"><strong>Job Types Total</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($serviceInvoice->job_total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Spare Parts Section -->
    @if($serviceInvoice->spareItems->count() > 0)
    <div class="parts-section">
        <div class="subsection-title">Spare Parts</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">Item Name</th>
                    <th style="width: 15%">Quantity</th>
                    <th style="width: 20%">Unit Price</th>
                    <th style="width: 20%">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceInvoice->spareItems as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3"><strong>Spare Parts Total</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($serviceInvoice->parts_total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Total Section -->
    <div class="total-section">
        <table class="total-table">
            @if($serviceInvoice->jobItems->count() > 0)
            <tr>
                <td class="label-col">Job Total</td>
                <td class="amount-col">Rs. {{ number_format($serviceInvoice->job_total, 2) }}</td>
            </tr>
            @endif
            @if($serviceInvoice->spareItems->count() > 0)
            <tr>
                <td class="label-col">Parts Total</td>
                <td class="amount-col">Rs. {{ number_format($serviceInvoice->parts_total, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label-col">GRAND TOTAL</td>
                <td class="amount-col">Rs. {{ number_format($serviceInvoice->grand_total, 2) }}</td>
            </tr>
            @if($serviceInvoice->status === 'finalized')
            <tr>
                <td class="label-col">Total Payments</td>
                <td class="amount-col">Rs. {{ number_format($serviceInvoice->getTotalPayments(), 2) }}</td>
            </tr>
            <tr style="background-color: #e74c3c; color: white;">
                <td class="label-col">Outstanding</td>
                <td class="amount-col">Rs. {{ number_format($serviceInvoice->getOutstandingAmount(), 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Notes Section -->
    @if($serviceInvoice->notes)
    <div class="notes">
        <div class="notes-title">Notes:</div>
        <div>{{ $serviceInvoice->notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>Terms & Conditions:</strong><br>
            • Payment is due within 30 days<br>
            • Warranty terms apply as per company policy
        </div>
        <div style="float: right; text-align: right;">
            <strong>Generated on:</strong> {{ now()->format('M d, Y g:i A') }}<br>
            <strong>Invoice #:</strong> {{ $serviceInvoice->invoice_no }}
        </div>
        <div style="clear: both; text-align: center; margin-top: 20px;">
            Thank you for your business!
        </div>
    </div>
</body>
</html> 