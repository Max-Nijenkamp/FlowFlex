<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyContext
{
    public function __construct(
        private readonly CompanyContext $context,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $company = $user->company;

            if ($company === null) {
                abort(403, 'No company associated with your account.');
            }

            if ($company->status === 'suspended') {
                abort(403, 'Your account has been suspended. Please contact support.');
            }

            if ($company->status === 'cancelled') {
                abort(403, 'Your account has been cancelled.');
            }

            $this->context->set($company);
            setPermissionsTeamId($company->id);

            if (class_exists(\Inertia\Inertia::class) && $request->header('X-Inertia')) {
                \Inertia\Inertia::share('currentCompany', [
                    'id'       => $company->id,
                    'name'     => $company->name,
                    'slug'     => $company->slug,
                    'branding' => $company->branding,
                    'locale'   => $company->locale,
                    'timezone' => $company->timezone,
                    'currency' => $company->currency,
                ]);
            }
        }

        return $next($request);
    }
}
