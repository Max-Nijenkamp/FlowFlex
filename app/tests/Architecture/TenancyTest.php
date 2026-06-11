<?php

declare(strict_types=1);

// CompanyScope may only be bypassed in the /admin panel and Support tenancy
// primitives. Anywhere else, withoutGlobalScope is a tenant-isolation leak.

arch('withoutGlobalScope is confined to admin + support tenancy code')
    ->expect('withoutGlobalScope')
    ->not->toBeUsedIn('App\Services')
    ->ignoring([
        'App\Filament\Admin',
        'App\Support',
    ]);

arch('tenancy primitives live under App\Support')
    ->expect('App\Support\Services\CompanyContext')
    ->toBeUsedIn('App\Http\Middleware\SetCompanyContext');
