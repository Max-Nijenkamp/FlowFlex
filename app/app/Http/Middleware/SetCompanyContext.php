<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the tenant company context after authentication on every web request.
 * Must run AFTER Authenticate (see architecture/filament-patterns #7).
 */
class SetCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user instanceof User) {
            $company = $user->company;
            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        return $next($request);
    }
}
