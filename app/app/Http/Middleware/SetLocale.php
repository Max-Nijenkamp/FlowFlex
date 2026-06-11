<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Settings\CompanyLocaleSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the company locale from CompanyLocaleSettings (core.settings is the
 * source of truth — companies.locale is only the creation-time seed).
 * Falls back to the app default when unauthenticated or context not yet set.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user instanceof User) {
            try {
                App::setLocale(app(CompanyLocaleSettings::class)->locale);
            } catch (\Throwable) {
                App::setLocale($user->company->locale);
            }
        }

        return $next($request);
    }
}
