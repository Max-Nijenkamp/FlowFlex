<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AcceptInvitationAction;
use App\Data\AcceptInvitationData;
use App\Exceptions\InvalidInvitationTokenException;
use App\Models\UserInvitation;
use App\Support\Scopes\CompanyScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /** Public invite-registration page (Vue + Inertia per frontend/_index.md). */
    public function showInviteRegistration(string $token): Response
    {
        $invitation = UserInvitation::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->with('company')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return Inertia::render('Auth/InviteRegister', [
            'email' => $invitation->email,
            'company' => $invitation->company->name,
            'token' => $token,
        ]);
    }

    public function acceptInvite(Request $request, string $token): RedirectResponse
    {
        $data = AcceptInvitationData::validateAndCreate([
            'token' => $token,
            ...$request->only(['first_name', 'last_name', 'password']),
        ]);

        try {
            AcceptInvitationAction::run($data);
        } catch (InvalidInvitationTokenException $e) {
            abort(404, $e->getMessage());
        }

        return redirect('/app');
    }
}
