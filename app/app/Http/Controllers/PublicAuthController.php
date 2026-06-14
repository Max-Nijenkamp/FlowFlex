<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PublicAuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request): SymfonyResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $request->session()->regenerate(); // session fixation guard (security.md)
        Auth::guard('web')->user()->forceFill(['last_login_at' => now()])->save();

        // The panel is a non-Inertia page: a plain redirect would be followed
        // by Inertia's XHR and rendered as a modal. location() forces a
        // full-page visit (409 + X-Inertia-Location for XHR, 302 otherwise).
        // Guard scope: a stale staff url.intended (guest visit to /admin)
        // must never hijack a customer login — same rule as
        // GuardScopedLoginResponse on the panel side.
        $intended = (string) $request->session()->pull('url.intended', '/app');
        $path = '/'.ltrim((string) parse_url($intended, PHP_URL_PATH), '/');

        if (preg_match('#^/(admin|horizon|pulse)(/|$)#', $path)) {
            $intended = '/app';
        }

        return Inertia::location($intended);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::broker('users')->sendResetLink($request->only('email'));

        // Same response whether or not the account exists — no enumeration.
        return back()->with('success', 'If that address exists, a reset link is on its way.');
    }

    public function showResetPassword(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(12)->uncompromised()],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['password' => __($status)]);
        }

        return redirect('/login')->with('success', 'Password reset — sign in with your new password.');
    }
}
