<?php

declare(strict_types=1);

namespace App\Mail;

use App\Data\ContactMessageData;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessageMail extends FlowFlexMailable
{
    public function __construct(
        public readonly ContactMessageData $data,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Website contact — '.$this->data->name,
            replyTo: [$this->data->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-message');
    }
}
