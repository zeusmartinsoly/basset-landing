<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSubmissionSubmitted extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public ContactSubmission $contactSubmission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رسالة تواصل جديدة — '.$this->contactSubmission->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-submission-submitted',
            with: [
                'submission' => $this->contactSubmission,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
