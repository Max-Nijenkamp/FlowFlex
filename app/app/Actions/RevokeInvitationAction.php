<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserInvitation;
use Lorisleiva\Actions\Concerns\AsAction;

class RevokeInvitationAction
{
    use AsAction;

    public function handle(string $invitationId): void
    {
        UserInvitation::query()->findOrFail($invitationId)->update(['revoked_at' => now()]);
    }
}
