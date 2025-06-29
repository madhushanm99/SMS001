<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $quotation->quotation_no }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Quotation: {{ $quotation->quotation_no }}</h2>
    <p><strong>Customer:</strong> {{ $quotation->customer_custom_id }}</p>
    <p><strong>Vehicle No:</strong> {{ $quotation->vehicle_no }}</p>
    <p><strong>Date:</strong> {{ $quotation->quotation_date }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ ucfirst($item->item_type) }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h4 style="text-align:right">Grand Total: Rs. {{ number_format($quotation->grand_total, 2) }}</h4>
</body>

</html>
