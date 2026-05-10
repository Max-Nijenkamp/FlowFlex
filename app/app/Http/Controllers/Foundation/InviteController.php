<?php

declare(strict_types=1);

namespace App\Http\Controllers\Foundation;

use App\Events\Foundation\UserActivated;
use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $invitation = UserInvitation::where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return redirect()->route('invite.expired');
        }

        return view('auth.invite', ['token' => $token, 'invitation' => $invitation]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return redirect()->route('invite.expired');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $invitation->user;

        $user->update([
            'password'          => Hash::make($request->string('password')),
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        event(new UserActivated($user, $invitation->company));

        auth()->guard('web')->login($user);

        return redirect('/app');
    }

    public function expired(): View
    {
        return view('auth.invite-expired');
    }
}
