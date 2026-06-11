<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks suspended/cancelled companies from tenant panels.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user instanceof User && in_array($user->company->subscription_status, ['suspended', 'cancelled'], true)) {
            abort(402, 'This workspace is suspended. Please contact billing.');
        }

        return $next($request);
    }
}
