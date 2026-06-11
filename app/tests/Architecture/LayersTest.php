<?php

declare(strict_types=1);

// FlowFlex architecture guard rails. See architecture/patterns/testing-pattern.
// Layer-specific rules (Data, Services, Controllers) are added as those layers
// are introduced by the first business modules.

arch('no debug statements leak into the codebase')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'die'])
    ->not->toBeUsed();

arch('strict types everywhere in app')
    ->expect('App')
    ->toUseStrictTypes();

arch('all Eloquent models use ULIDs')
    ->expect('App\Models')
    ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUlids')
    ->ignoring([
        // Sushi static catalog (platform-level, code-defined rows).
        App\Models\Core\ModuleCatalog::class,
    ]);

arch('models soft delete unless append-only by design')
    ->expect('App\Models')
    ->toUseTrait('Illuminate\Database\Eloquent\SoftDeletes')
    ->ignoring([
        App\Models\Core\ModuleCatalog::class,
        // Append-only rows: activation history, invoice lines, invitations.
        App\Models\Core\CompanyModuleSubscription::class,
        App\Models\Core\BillingInvoiceLine::class,
        App\Models\Core\UserInvitation::class,
    ]);

arch('append-only core models still use ULIDs')
    ->expect([
        App\Models\Core\CompanyModuleSubscription::class,
        App\Models\Core\BillingInvoiceLine::class,
        App\Models\Core\UserInvitation::class,
    ])
    ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUlids');
