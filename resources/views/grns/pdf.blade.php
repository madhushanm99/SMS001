<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>GRN - {{ $grn->grn_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <h2>Goods Received Note</h2>
    <p><strong>GRN No:</strong> {{ $grn->grn_no }}</p>
    <p><strong>Date:</strong> {{ $grn->grn_date }}</p>
    <p><strong>Supplier:</strong> {{ $supplier->Supp_Name ?? $grn->supp_Cus_ID }}</p>
    <p><strong>PO No:</strong> {{ $grn->po_No ?? '-' }}</p>
    <p><strong>Invoice No:</strong> {{ $grn->invoice_no ?? '-' }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount (%)</th>
                <th>Discount Value</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDiscount = 0;
            @endphp
            @foreach ($items as $i => $item)
                @php
                    $discountValue = ($item->price * $item->qty_received * ($item->discount ?? 0)) / 100;
                    $totalDiscount += $discountValue;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item_ID }}</td>
                    <td>{{ $item->item_Name }}</td>
                    <td>{{ $item->qty_received }}</td>
                    <td>Rs. {{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->discount ?? 0, 2) }}%</td>
                    <td>Rs. {{ number_format($discountValue, 2) }}</td>
                    <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: right; font-weight: bold;">
        <p>Total Discount: Rs. {{ number_format($totalDiscount, 2) }}</p>
        <p>Grand Total: Rs. {{ number_format($items->sum('line_total'), 2) }}</p>
    </div>
</body>

</html>
