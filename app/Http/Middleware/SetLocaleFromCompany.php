<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('tenant')->check()) {
            $sessionLocale = session('locale') ?? $request->cookie('filament_language_switch_locale');
            $companyLocale = auth('tenant')->user()->company->locale?->value ?? 'en';

            $allowed = ['en', 'nl', 'de'];
            $locale = in_array($sessionLocale, $allowed, true) ? $sessionLocale : $companyLocale;

            app()->setLocale($locale);
        }

        return $next($request);
    }
}
