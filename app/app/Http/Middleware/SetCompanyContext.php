<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Company;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetCompanyContext
{
    public function __construct(private readonly CompanyContext $context) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if ($user === null || ! isset($user->company_id)) {
            return $next($request);
        }

        $company = Company::withoutGlobalScopes()->findOrFail($user->company_id);

        if ($company->status === 'suspended') {
            abort(403, 'Your account has been suspended.');
        }

        $this->context->set($company);

        // Scope Spatie Permission to this company
        setPermissionsTeamId($company->id);

        return $next($request);
    }
}
