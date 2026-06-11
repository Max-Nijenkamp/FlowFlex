<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateInvitationData;
use App\Mail\InvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class SendInvitationAction
{
    use AsAction;

    public function handle(CreateInvitationData $data): UserInvitation
    {
        if (User::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages(['email' => 'This email already belongs to a user in this company.']);
        }

        if (UserInvitation::query()->pending()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages(['email' => 'This email already has a pending invitation.']);
        }

        $invitation = UserInvitation::create([
            'email' => $data->email,
            'token' => (string) Str::uuid(),
            'role' => $data->role,
            'invited_by' => Auth::guard('web')->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $company = app(CompanyContext::class)->current();

        Mail::to($invitation->email)->send(new InvitationMail(
            company_id: $company->id,
            companyName: $company->name,
            inviteUrl: url("/register/invite/{$invitation->token}"),
            roleName: $invitation->role,
        ));

        return $invitation;
    }
}
