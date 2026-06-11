<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API twin of SetCompanyContext: resolves tenant context from the Sanctum
 * token's user. Runs on every /api request after auth resolves.
 */
class SetCompanyContextFromToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            $company = $user->company;
            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        return $next($request);
    }
}
