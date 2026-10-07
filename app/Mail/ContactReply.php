<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class ContactReply extends Mailable
{
    public function __construct(public ContactMessage $contactMessage, public string $replyBody)
    {
    }

    public function envelope(): Envelope
    {
        $subject = 'Re: ' . ($this->contactMessage->subject ?: 'Your message');

        $replyTo = config('mail.reply_to.address');

        return new Envelope(
            replyTo: $replyTo ? [new Address($replyTo, config('mail.reply_to.name'))] : [],
            subject: $subject . ' - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'email.contact-reply',
            with: [
                'name' => $this->contactMessage->name,
                'replyBody' => $this->replyBody,
                'originalMessage' => $this->contactMessage->message,
                'clinic' => config('app.name'),
            ],
        );
    }
}
