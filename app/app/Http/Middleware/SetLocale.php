<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the tenant's locale: the company's locale (per-company default).
 * Falls back to the app default when unauthenticated.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user instanceof User) {
            App::setLocale($user->company->locale);
        }

        return $next($request);
    }
}
