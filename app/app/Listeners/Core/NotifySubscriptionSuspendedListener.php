<?php

declare(strict_types=1);

namespace App\Listeners\Core;

use App\Events\Core\CompanySubscriptionSuspended;
use App\Models\Company;
use App\Models\User;
use App\Notifications\Core\SubscriptionSuspendedNotification;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySubscriptionSuspendedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public function handle(CompanySubscriptionSuspended $event): void
    {
        // Queued listener: restore tenant context from the event's scalar company_id.
        $company = Company::query()->withoutGlobalScopes()->findOrFail($event->company_id);
        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        User::query()->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
            ->get()
            ->each->notify(new SubscriptionSuspendedNotification($event->reason));
    }
}
