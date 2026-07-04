<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateInvitationData;
use App\Mail\InvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class SendInvitationAction
{
    use AsAction;

    public function handle(CreateInvitationData $data): UserInvitation
    {
        $company = app(CompanyContext::class)->current();

        if ($data->role === 'owner') {
            throw ValidationException::withMessages(['role' => 'Ownership is transferred, never granted by invite.']);
        }

        $roleExists = Role::query()
            ->where('company_id', $company->id)
            ->where('name', $data->role)
            ->exists();

        if (! $roleExists) {
            throw ValidationException::withMessages(['role' => 'That role does not exist in this workspace.']);
        }

        if (User::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages(['email' => 'This email already belongs to a member of your workspace.']);
        }

        $hasPending = UserInvitation::query()
            ->where('email', $data->email)
            ->get()
            ->contains(fn (UserInvitation $invitation): bool => $invitation->isPending());

        if ($hasPending) {
            throw ValidationException::withMessages(['email' => 'This email already has a pending invitation.']);
        }

        $causer = Auth::user();

        $invitation = UserInvitation::query()->create([
            'company_id' => $company->id,
            'email' => $data->email,
            'role' => $data->role,
            'token' => (string) Str::uuid(),
            'invited_by' => $causer instanceof User ? $causer->id : null,
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->queue(InvitationMail::forInvitation($invitation));

        app(AuditLogger::class)->log(
            'core.invitation-sent',
            $invitation,
            $causer instanceof User ? $causer : null,
            ['email' => $data->email, 'role' => $data->role],
        );

        return $invitation;
    }
}
