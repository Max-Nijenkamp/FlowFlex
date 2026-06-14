<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * Guard-scoped login redirect. Laravel stores ONE `url.intended` per session,
 * so a guest visit to /admin followed by a customer login (or vice versa)
 * bounced users to the other guard's login page. Only honor the intended URL
 * when it belongs to the guard that just signed in.
 */
class GuardScopedLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // Livewire component tests run without a request-bound session store.
        $intended = $request->hasSession() ? $request->session()->pull('url.intended') : session()->pull('url.intended');
        $isStaffPanel = Filament::getCurrentPanel()?->getAuthGuard() === 'admin';

        if (is_string($intended) && $this->intendedMatchesGuard($intended, $isStaffPanel)) {
            return redirect()->to($intended);
        }

        return redirect()->to(Filament::getUrl());
    }

    private function intendedMatchesGuard(string $intended, bool $isStaffPanel): bool
    {
        $path = '/'.ltrim((string) parse_url($intended, PHP_URL_PATH), '/');
        $isStaffUrl = (bool) preg_match('#^/(admin|horizon|pulse)(/|$)#', $path);

        return $isStaffPanel === $isStaffUrl;
    }
}
