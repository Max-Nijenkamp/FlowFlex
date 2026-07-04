<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AcceptInvitationData;
use App\Events\InvitationAccepted;
use App\Exceptions\InvalidInvitationTokenException;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Public accept flow (core.invitation-system/accept-flow): validates the
 * token under a row lock (double-accept race safe), creates the user in the
 * invitation's company, assigns the invited role under that team, logs in.
 */
class AcceptInvitationAction
{
    use AsAction;

    public function handle(AcceptInvitationData $data): User
    {
        return DB::transaction(function () use ($data): User {
            /** @var UserInvitation|null $invitation */
            $invitation = UserInvitation::query()
                ->withoutGlobalScopes()
                ->where('token', $data->token)
                ->lockForUpdate()
                ->first();

            if ($invitation === null || ! $invitation->isPending()) {
                throw InvalidInvitationTokenException::make();
            }

            $user = new User([
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'password' => $data->password,
            ]);
            $user->company_id = $invitation->company_id;
            $user->email = $invitation->email;
            $user->email_verified_at = Carbon::now(); // the invite email IS the verification
            $user->save();

            $invitation->update(['accepted_at' => now()]);

            // Assign the invited role under the invitation's team.
            app(CompanyContext::class)->set($invitation->company()->withoutGlobalScopes()->firstOrFail());
            setPermissionsTeamId($invitation->company_id);
            $user->assignRole($invitation->role);

            InvitationAccepted::dispatch($invitation->company_id, $user->id, $invitation->role);

            app(AuditLogger::class)->log('core.invitation-accepted', $user, $user, ['role' => $invitation->role]);

            /** @var StatefulGuard $guard */
            $guard = Auth::guard('web');
            $guard->login($user);

            return $user;
        });
    }
}
