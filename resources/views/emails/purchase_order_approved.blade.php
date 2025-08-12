<div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
    <h2>Purchase Order Approved</h2>

    <p>Dear {{ $supplier->Supp_Name ?? 'Supplier' }},</p>

    <p>The following Purchase Order has been approved:</p>

    <ul>
        <li><strong>PO No:</strong> {{ $po->po_No ?? $po->po_Auto_ID }}</li>
        <li><strong>Date:</strong> {{ $po->po_date }}</li>
        <li><strong>Total:</strong> {{ number_format($po->grand_Total, 2) }}</li>
        <li><strong>Status:</strong> {{ ucfirst($po->orderStatus) }}</li>
    </ul>

    <p>Please proceed accordingly. If you have any questions, reply to this email.</p>

    <p>Thank you.</p>
</div>


