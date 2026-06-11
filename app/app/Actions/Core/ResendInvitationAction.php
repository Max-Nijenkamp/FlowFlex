<?php

declare(strict_types=1);

namespace App\Actions\Core;

use App\Mail\Core\InvitationMail;
use App\Models\Core\UserInvitation;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendInvitationAction
{
    use AsAction;

    public function handle(string $invitationId): UserInvitation
    {
        $invitation = UserInvitation::query()->findOrFail($invitationId);

        // New token invalidates the old link; expiry restarts.
        $invitation->update([
            'token' => (string) Str::uuid(),
            'expires_at' => now()->addDays(7),
            'revoked_at' => null,
        ]);

        $company = app(CompanyContext::class)->current();

        Mail::to($invitation->email)->send(new InvitationMail(
            company_id: $company->id,
            companyName: $company->name,
            inviteUrl: url("/register/invite/{$invitation->token}"),
            roleName: $invitation->role,
        ));

        return $invitation->refresh();
    }
}
