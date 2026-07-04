<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CompanySubscriptionSuspended;
use App\Models\User;
use App\Notifications\SubscriptionSuspendedNotification;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySubscriptionSuspendedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(CompanySubscriptionSuspended $event): void
    {
        WithCompanyContext::restore($event->company_id);

        // Every owner/admin hears about a suspension — mail must go out even
        // though the workspace itself is blocked.
        User::query()
            ->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->get()
            ->filter(fn (User $user): bool => $user->hasRole('owner') || $user->hasRole('admin'))
            ->each(fn (User $user) => $user->notify(new SubscriptionSuspendedNotification($event->reason)));
    }
}
