<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Owners of a company with incomplete setup land on the wizard first.
 * Non-owners and completed companies pass through untouched.
 */
class RedirectToSetupWizard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user instanceof User
            && $user->company->setup_completed_at === null
            && $user->hasRole('owner')
            && ! $request->routeIs('filament.app.pages.setup-wizard')
            && ! $request->routeIs('filament.app.auth.*')
        ) {
            return redirect('/app/setup-wizard');
        }

        return $next($request);
    }
}
