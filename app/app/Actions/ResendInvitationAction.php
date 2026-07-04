<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\InvitationMail;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendInvitationAction
{
    use AsAction;

    public function handle(string $invitationId): UserInvitation
    {
        return DB::transaction(function () use ($invitationId): UserInvitation {
            /** @var UserInvitation $invitation */
            $invitation = UserInvitation::query()->whereKey($invitationId)->lockForUpdate()->firstOrFail();

            if ($invitation->accepted_at !== null || $invitation->revoked_at !== null) {
                throw ValidationException::withMessages(['invitation' => 'This invitation is closed and cannot be resent.']);
            }

            // Rotating the token invalidates every previously mailed link.
            $invitation->update([
                'token' => (string) Str::uuid(),
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($invitation->email)->queue(InvitationMail::forInvitation($invitation));

            return $invitation;
        });
    }
}
