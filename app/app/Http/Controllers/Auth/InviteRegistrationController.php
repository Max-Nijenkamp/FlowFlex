<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\AcceptInvitationAction;
use App\Data\AcceptInvitationData;
use App\Exceptions\InvalidInvitationTokenException;
use App\Models\UserInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Public invite registration (core.invitation-system/accept-flow).
 * Blade fallback — swapped for InviteRegister.vue when the Vue + Inertia
 * public site ships (deviation noted in the roadmap 2026-07-04).
 */
class InviteRegistrationController
{
    public function show(string $token): View
    {
        $invitation = UserInvitation::query()
            ->withoutGlobalScopes()
            ->where('token', $token)
            ->first();

        return view('auth.invite-register', [
            'invitation' => $invitation !== null && $invitation->isPending() ? $invitation : null,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        try {
            AcceptInvitationAction::run(new AcceptInvitationData(
                token: $token,
                first_name: $validated['first_name'],
                last_name: $validated['last_name'],
                password: $validated['password'],
            ));
        } catch (InvalidInvitationTokenException $e) {
            return redirect()->route('invite.register', ['token' => $token])
                ->withErrors(['token' => $e->getMessage()]);
        }

        return redirect('/app');
    }
}
