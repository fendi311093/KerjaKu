<?php

namespace App\Mail;

use App\Models\FormMultipleUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMultipleUploadMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formMultipleUpload;

    /**
     * Create a new message instance.
     */
    public function __construct(FormMultipleUpload $formMultipleUpload)
    {
        $this->formMultipleUpload = $formMultipleUpload;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->formMultipleUpload->to,
            cc: $this->formMultipleUpload->cc,
            subject: $this->formMultipleUpload->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.multiple-upload',
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
        foreach ($this->formMultipleUpload->attachments as $filePath) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/public/' . $filePath));
        }
        return $attachments;
    }
}
