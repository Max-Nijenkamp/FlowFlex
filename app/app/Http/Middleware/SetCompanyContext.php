<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs AFTER Authenticate in the /app auth chain, with isPersistent: true so
 * Livewire update POSTs re-run it (the null-team 403 family otherwise).
 */
class SetCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            $company = Company::withTrashed()->findOrFail($user->company_id);

            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        return $next($request);
    }
}
