<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserInvitation;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class RevokeInvitationAction
{
    use AsAction;

    public function handle(string $invitationId): void
    {
        /** @var UserInvitation $invitation */
        $invitation = UserInvitation::query()->findOrFail($invitationId);

        if ($invitation->accepted_at !== null) {
            throw ValidationException::withMessages(['invitation' => 'This invitation was already accepted.']);
        }

        $invitation->update(['revoked_at' => now()]);
    }
}
