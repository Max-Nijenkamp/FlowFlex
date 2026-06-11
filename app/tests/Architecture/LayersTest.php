<?php

declare(strict_types=1);
use App\Models\Activity;
use App\Models\BillingInvoiceLine;
use App\Models\CompanyModuleSubscription;
use App\Models\ConsentLog;
use App\Models\CRM\QuoteLine;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\FiscalPeriod;
use App\Models\Finance\InvoiceLine;
use App\Models\Finance\JournalLine;
use App\Models\Finance\Payment;
use App\Models\HR\DeiAttribute;
use App\Models\HR\DeiSnapshot;
use App\Models\HR\EmergencyContact;
use App\Models\HR\EmployeeBenefit;
use App\Models\HR\Interview;
use App\Models\HR\Offer;
use App\Models\HR\OnboardingPlanTask;
use App\Models\HR\OnboardingTask;
use App\Models\HR\PlannedRole;
use App\Models\HR\Review;
use App\Models\HR\ReviewGoal;
use App\Models\HR\SalaryHistory;
use App\Models\HR\ShiftSwapRequest;
use App\Models\HR\TimeEntry;
use App\Models\ModuleCatalog;
use App\Models\NotificationPreference;
use App\Models\UserInvitation;
use App\Models\WebhookDelivery;

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
        // Hard-deleted on GDPR erasure (data-lifecycle) — soft delete would defeat it.
        EmergencyContact::class,
        // Template/plan line + pivot-style rows, managed with their parents.
        OnboardingTask::class,
        OnboardingPlanTask::class,
        JournalLine::class,
        InvoiceLine::class,
        Payment::class,
        FiscalPeriod::class,
        BankTransaction::class,
        QuoteLine::class,
        // HR append-only / pivot-style / aggregate rows.
        Interview::class,
        Offer::class,
        Review::class,
        ReviewGoal::class,
        TimeEntry::class,
        ShiftSwapRequest::class,
        EmployeeBenefit::class,
        SalaryHistory::class,
        PlannedRole::class,
        DeiAttribute::class,
        DeiSnapshot::class,
    ]);

arch('append-only core models still use ULIDs')
    ->expect([
        CompanyModuleSubscription::class,
        BillingInvoiceLine::class,
        UserInvitation::class,
    ])
    ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUlids');
