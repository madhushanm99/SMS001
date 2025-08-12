<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PurchaseOrderApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public object $po;
    public object $supplier;

    public function __construct(object $po, object $supplier)
    {
        $this->po = $po;
        $this->supplier = $supplier;
    }

    public function build()
    {
        // Prepare data for PDF (items join)
        $items = DB::table('po__Item')
            ->join('item', 'po__Item.item_ID', '=', 'item.item_ID')
            ->where('po__Item.po_Auto_ID', $this->po->po_Auto_ID)
            ->select('po__Item.*', 'item.item_Name as item_name')
            ->get();

        $pdf = Pdf::loadView('purchase_orders.pdf', [
            'po' => $this->po,
            'items' => $items,
            'supplier' => $this->supplier,
        ]);

        $filename = 'PO-' . ($this->po->po_No ?? $this->po->po_Auto_ID) . '.pdf';

        return $this->subject('Purchase Order Approved: ' . ($this->po->po_No ?? $this->po->po_Auto_ID))
            ->view('emails.purchase_order_approved')
            ->with([
                'po' => $this->po,
                'supplier' => $this->supplier,
            ])
            ->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}


