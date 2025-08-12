<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseReturnCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public object $return;
    public object $supplier;

    public function __construct(object $return, object $supplier)
    {
        $this->return = $return;
        $this->supplier = $supplier;
    }

    public function build()
    {
        $pdf = Pdf::loadView('purchase_returns.pdf', [
            'return' => $this->return,
            'supplier' => $this->supplier,
        ]);

        $filename = 'PR-' . ($this->return->return_no ?? $this->return->id) . '.pdf';

        return $this->subject('Purchase Return Created: ' . ($this->return->return_no ?? $this->return->id))
            ->view('emails.purchase_return_created')
            ->with([
                'return' => $this->return,
                'supplier' => $this->supplier,
            ])
            ->attachData($pdf->output(), $filename, ['mime' => 'application/pdf']);
    }
}


