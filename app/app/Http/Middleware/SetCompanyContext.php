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

            // Anything that touched roles/permissions BEFORE the team id was
            // set (e.g. shared props in the global web group) cached them
            // empty — spatie's loadMissing() would keep that empty set for
            // the whole request. Force a team-scoped reload.
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');
        }

        return $next($request);
    }
}
