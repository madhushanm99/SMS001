<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use App\Models\ServiceInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfContent;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice, $pdfContent = null, $customMessage = null)
    {
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->invoice instanceof ServiceInvoice
            ? "Service Invoice #{$this->invoice->invoice_no} - " . config('app.name')
            : "Invoice #{$this->invoice->invoice_no} - " . config('app.name');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'customer' => $this->invoice->customer,
                'customMessage' => $this->customMessage,
                'isServiceInvoice' => $this->invoice instanceof ServiceInvoice
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfContent) {
            // Use provided PDF content
            return [
                Attachment::fromData(
                    fn () => $this->pdfContent,
                    "invoice-{$this->invoice->invoice_no}.pdf"
                )->withMime('application/pdf')
            ];
        }

        // Generate PDF based on invoice type
        if ($this->invoice instanceof ServiceInvoice) {
            $pdf = Pdf::loadView('service_invoices.pdf', ['serviceInvoice' => $this->invoice]);
        } else {
            $pdf = Pdf::loadView('sales_invoices.pdf', ['invoice' => $this->invoice]);
        }

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "invoice-{$this->invoice->invoice_no}.pdf"
            )->withMime('application/pdf')
        ];
    }
}
