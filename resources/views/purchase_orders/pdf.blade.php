<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $po->po_No }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 0; }
    </style>
</head>
<body>
    <h2>Purchase Order - {{ $po->po_No }}</h2>
    <p><strong>Date:</strong> {{ $po->po_date }}</p>
    <p><strong>Supplier:</strong> {{ $supplier->Supp_Name ?? 'N/A' }}</p>
    <p><strong>Reference No:</strong> {{ $po->Reff_No }}</p>
    <p><strong>Status:</strong> {{ ucfirst($po->orderStatus) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item ID</th>
                <th>Description</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item_ID }}</td>
                <td>{{ $item->item_name }}</td>
                <td>Rs. {{ number_format($item->price, 2) }}</td>
                <td>{{ $item->qty }}</td>
                <td>Rs. {{ number_format($item->line_Total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right;"><strong>Grand Total: Rs. {{ number_format($po->grand_Total, 2) }}</strong></p>

    <p><strong>Note:</strong> {{ $po->note }}</p>
</body>
</html>

