<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanctum/API equivalent of SetCompanyContext — resolves the tenant from the
 * token's user. Token-to-company binding hardening lands with core.api-clients.
 */
class SetCompanyContextFromToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        if ($user instanceof User) {
            $company = $user->company()->firstOrFail();

            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        return $next($request);
    }
}
