<?php

declare(strict_types=1);

namespace App\Mail\Foundation;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Company $company,
        public readonly string $inviteToken,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to {$this->company->name} on FlowFlex",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.foundation.user-invited',
            with: [
                'user'        => $this->user,
                'company'     => $this->company,
                'acceptUrl'   => url("/invite/{$this->inviteToken}"),
                'expiresAt'   => now()->addDays(7)->format('F j, Y'),
            ],
        );
    }
}
