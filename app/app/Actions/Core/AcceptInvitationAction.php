<?php

declare(strict_types=1);

namespace App\Actions\Core;

use App\Data\Core\AcceptInvitationData;
use App\Exceptions\Core\InvalidInvitationTokenException;
use App\Models\Core\UserInvitation;
use App\Models\User;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class AcceptInvitationAction
{
    use AsAction;

    /**
     * Public path — runs without an authenticated session; company context is
     * derived from the invitation itself.
     */
    public function handle(AcceptInvitationData $data): User
    {
        $invitation = UserInvitation::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where('token', $data->token)
            ->first();

        if ($invitation === null || ! $invitation->isUsable()) {
            throw new InvalidInvitationTokenException();
        }

        return DB::transaction(function () use ($invitation, $data): User {
            $user = User::create([
                'company_id' => $invitation->company_id,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $invitation->email,
                'password' => Hash::make($data->password),
                'email_verified_at' => now(), // invite link proves mailbox ownership
            ]);

            // Assign the invited role under the right company team.
            $company = $user->company;
            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
            $user->assignRole($invitation->role);

            $invitation->forceFill(['accepted_at' => now()])->save();

            Auth::guard('web')->login($user);

            return $user;
        });
    }
}
