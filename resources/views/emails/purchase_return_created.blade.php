<div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
    <h2>Purchase Return Created</h2>
    <p>Dear {{ $supplier->Supp_Name ?? 'Supplier' }},</p>
    <p>A purchase return has been created with the following details:</p>
    <ul>
        <li><strong>Return No:</strong> {{ $return->return_no }}</li>
        <li><strong>GRN No:</strong> {{ $return->grn_no }}</li>
        <li><strong>Date:</strong> {{ optional($return->created_at)->format('Y-m-d') }}</li>
    </ul>
    <p>The detailed Purchase Return PDF is attached with this email.</p>
    <p>Thank you.</p>
 </div>


