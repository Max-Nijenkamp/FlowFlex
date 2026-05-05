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
            $locale = auth('tenant')->user()->company->locale?->value ?? 'en';
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
