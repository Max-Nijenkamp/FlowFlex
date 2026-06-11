<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DsarRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DsarDeadlineReminderCommand extends Command
{
    protected $signature = 'flowflex:dsar-deadline-reminders';

    protected $description = 'Warn privacy managers when DSAR deadlines approach (-7d and -1d, once each)';

    public function handle(): int
    {
        $open = DsarRequest::query()->withoutGlobalScopes()
            ->whereNull('completed_at')
            ->whereIn(
                DB::raw('date(due_at)'),
                [now()->addDays(7)->toDateString(), now()->addDay()->toDateString()],
            )
            ->get();

        foreach ($open as $request) {
            $days = (int) now()->diffInDays($request->due_at, false);

            User::query()->withoutGlobalScopes()
                ->where('company_id', $request->company_id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
                ->get()
                ->each(function (User $owner) use ($request, $days): void {
                    Log::info('DSAR deadline reminder', [
                        'dsar_request_id' => $request->id,
                        'owner' => $owner->id,
                        'days_left' => $days,
                    ]);
                });
        }

        $this->info("Processed {$open->count()} deadline reminders.");

        return self::SUCCESS;
    }
}
