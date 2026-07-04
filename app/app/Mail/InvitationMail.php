<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\UserInvitation;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvitationMail extends FlowFlexMailable
{
    public function __construct(
        public readonly string $inviteeEmail,
        public readonly string $acceptUrl,
        public readonly string $roleName,
    ) {
        parent::__construct();
    }

    public static function forInvitation(UserInvitation $invitation): self
    {
        return new self(
            inviteeEmail: $invitation->email,
            acceptUrl: route('invite.register', ['token' => $invitation->token]),
            roleName: $invitation->role,
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You have been invited to FlowFlex');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.invitation');
    }
}
