<?php

declare(strict_types=1);
use App\Models\Core\Activity;
use App\Models\Core\BillingInvoiceLine;
use App\Models\Core\CompanyModuleSubscription;
use App\Models\Core\ConsentLog;
use App\Models\Core\ModuleCatalog;
use App\Models\Core\NotificationPreference;
use App\Models\Core\UserInvitation;
use App\Models\Core\WebhookDelivery;

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
        ModuleCatalog::class,
    ]);

arch('models soft delete unless append-only by design')
    ->expect('App\Models')
    ->toUseTrait('Illuminate\Database\Eloquent\SoftDeletes')
    ->ignoring([
        ModuleCatalog::class,
        // Append-only / pruned-by-retention rows — never soft-deleted.
        CompanyModuleSubscription::class,
        BillingInvoiceLine::class,
        UserInvitation::class,
        Activity::class,
        WebhookDelivery::class,
        ConsentLog::class,
        // Per-user toggle rows, upserted in place.
        NotificationPreference::class,
    ]);

arch('append-only core models still use ULIDs')
    ->expect([
        CompanyModuleSubscription::class,
        BillingInvoiceLine::class,
        UserInvitation::class,
    ])
    ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUlids');
