<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Return #{{ $return->return_no }}</title>
    <style>
        body {
            font-family: Arial;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h3>Purchase Return</h3>
    <p><strong>Return No:</strong> {{ $return->return_no }}</p>
    <p><strong>GRN No:</strong> {{ $return->grn_no }}</p>
    <p><strong>Supplier:</strong> {{ $supplier->Supp_Name ?? $return->supp_Cus_ID }}</p>
    <p><strong>Returned By:</strong> {{ $return->returned_by }}</p>
    <p><strong>Date:</strong> {{ $return->created_at->format('Y-m-d') }}</p>
    <p><strong>Note:</strong> {{ $return->note }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price (After Discount)</th>
                <th>Reason</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($return->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item_ID }} - {{ $item->item_Name }}</td>
                    <td>{{ $item->qty_returned }}</td>
                    <td>Rs. {{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->reason ?? '-' }}</td>
                    <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-weight: bold;">Total: Rs.
        {{ number_format($return->items->sum('line_total'), 2) }}</p>
</body>

</html>
