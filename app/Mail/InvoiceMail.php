<?php
namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfContent;
    public $pdfFilename;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, $pdfContent, $pdfFilename)
    {
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
        $this->pdfFilename = $pdfFilename;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->invoice->type === 'quote' 
            ? "Devis #{$this->invoice->reference} - " . config('app.name')
            : "Facture #{$this->invoice->reference} - " . config('app.name');

        return new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'noreply@factupro.com'),
            replyTo: config('mail.reply_to.address', 'contact@factupro.com'),
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
                'client' => $this->invoice->client,
                'isQuote' => $this->invoice->type === 'quote',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}