<?php

declare(strict_types=1);

namespace App\Mail;

use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvitationMail extends FlowFlexMailable
{
    public function __construct(
        public readonly string $company_id,
        public readonly string $companyName,
        public readonly string $inviteUrl,
        public readonly string $roleName,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "You're invited to join {$this->companyName} on FlowFlex");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invitation',
            with: [
                ...$this->branding(),
                'inviteUrl' => $this->inviteUrl,
                'companyName' => $this->companyName,
                'roleName' => $this->roleName,
            ],
        );
    }
}
