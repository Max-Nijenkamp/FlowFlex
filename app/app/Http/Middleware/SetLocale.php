<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'nl', 'de', 'fr', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        if (auth()->check()) {
            $userLocale = auth()->user()->locale;
            if ($userLocale && in_array($userLocale, self::SUPPORTED, true)) {
                return $userLocale;
            }
        }

        $acceptLocale = substr($request->header('Accept-Language', 'en'), 0, 2);
        if (in_array($acceptLocale, self::SUPPORTED, true)) {
            return $acceptLocale;
        }

        return config('app.locale', 'en');
    }
}
