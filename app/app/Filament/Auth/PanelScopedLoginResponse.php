<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * Both panels share one session, so a stored "intended" URL from a visit to
 * /admin would hijack a login performed on /app (and vice versa), bouncing
 * users to the other panel's login. Only honor the intended URL when it
 * belongs to the panel the login happened on.
 */
class PanelScopedLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $panelUrl = Filament::getUrl();
        $intended = session()->pull('url.intended');

        if (
            is_string($intended)
            && ($intended === $panelUrl || str_starts_with($intended, $panelUrl.'/'))
        ) {
            return redirect()->to($intended);
        }

        return redirect()->to($panelUrl);
    }
}
