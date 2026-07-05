<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Crm\Activity;
use App\Models\User;
use App\Notifications\ActivityReminderNotification;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

/**
 * Scheduled scan for due-within-a-day and overdue CRM tasks
 * (crm.activities/task-reminders). reminded_at is the remind-once
 * guard. Runs across all tenants — deliberate unscoped read with an
 * explicit soft-delete filter; context restored per activity so
 * notification routing stays tenant-correct.
 */
class SendActivityRemindersCommand extends Command
{
    protected $signature = 'crm:send-activity-reminders';

    protected $description = 'Notify owners of due and overdue CRM tasks';

    public function handle(): int
    {
        /** @var Collection<int, Activity> $due */
        $due = Activity::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('type', 'task')
            ->where('is_complete', false)
            ->whereNull('reminded_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->addDay())
            ->get();

        foreach ($due as $activity) {
            WithCompanyContext::restore($activity->company_id);

            $owner = User::query()->find($activity->owner_id);

            if (! $owner instanceof User) {
                continue;
            }

            $owner->notify(new ActivityReminderNotification(
                $activity->subject,
                $activity->due_at !== null && $activity->due_at->isPast(),
            ));

            $activity->update(['reminded_at' => now()]);
        }

        app(CompanyContext::class)->forget();

        $this->info("Reminders sent: {$due->count()}");

        return self::SUCCESS;
    }
}
