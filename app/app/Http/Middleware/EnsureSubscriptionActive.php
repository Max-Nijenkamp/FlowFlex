<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse subscription gate on /app. Full billing flows (grace periods,
 * payment recovery pages) belong to core.billing-engine; until it ships,
 * suspended/cancelled companies are blocked with a clear 402.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(CompanyContext::class);

        if ($context->currentId() !== null) {
            $status = $context->current()->subscription_status;

            if (! in_array($status, ['trial', 'active'], true)) {
                abort(402, 'This workspace is suspended. Contact FlowFlex support to reactivate.');
            }
        }

        return $next($request);
    }
}
