<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;
    private $pdf_files;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $pdf_files)
    {
        $this->data = $data;
        $this->pdf_files = $pdf_files;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config("common.email_from"), $this->data['invoice_due_name_from']),
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.test",
            with: $this->data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        $attachments[] =  Attachment::fromPath(public_path('files/'.$this->data['file_name']))
        ->as('letter.docx')
            ->withMime('application/docx');

        if(count($this->pdf_files) > 0 ){
            foreach($this->pdf_files as $attach){
                $attachments[] = Attachment::fromPath(public_path('files/'.$attach))->as($attach)->withMime('application/pdf');
            }
        }
        // dd($attachments);
        return $attachments;
    }

}
