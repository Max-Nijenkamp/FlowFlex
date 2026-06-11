<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Core\AcceptInvitationAction;
use App\Data\Core\AcceptInvitationData;
use App\Exceptions\Core\InvalidInvitationTokenException;
use App\Models\Core\UserInvitation;
use App\Support\Scopes\CompanyScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Public invite-registration page. Blade for now — swapped to the
     * Vue + Inertia page when the public site ships (frontend phase).
     */
    public function showInviteRegistration(string $token): View
    {
        $invitation = UserInvitation::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->with('company')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('auth.invite-register', [
            'email' => $invitation->email,
            'companyName' => $invitation->company->name,
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
