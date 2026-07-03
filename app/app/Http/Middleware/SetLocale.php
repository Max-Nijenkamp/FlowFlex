<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Company locale drives the app locale; per-user preference layers on top
 * when core.i18n ships user-level language.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(CompanyContext::class);

        if ($context->currentId() !== null) {
            app()->setLocale($context->current()->locale);
        }

        return $next($request);
    }
}
