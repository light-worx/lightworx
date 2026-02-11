<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maildata;

    /**
     * Create a new message instance.
     */
    public function __construct($maildata)
    {
        $this->maildata = $maildata;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $lightworx = new Address('admin@lightworx.co.za', 'Michael Bishop');
        return new Envelope(
            from: $lightworx,
            replyTo: [$lightworx],
            to: [new Address($this->maildata['clientEmail'], $this->maildata['clientName'])],
            bcc: [$lightworx],
            subject: 'Lightworx Invoice #' . $this->maildata['invoiceId'] . " (" . date('Y-m-d') . ")",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice',
            with: [
                'maildata' => $this->maildata,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachdata = $this->maildata['attachData'] ?? null;
        $attachname = $this->maildata['attachName'] ?? null;
        if ($attachdata && $attachname) {
            return [
                Attachment::fromData(fn () => base64_decode($attachdata), $attachname)->withMime('application/pdf')
            ];
        }
    }
}
