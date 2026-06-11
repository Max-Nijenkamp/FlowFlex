<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;

class PruneAuditLogCommand extends Command
{
    protected $signature = 'flowflex:prune-audit-log {--months=24 : Default retention when a company has no setting}';

    protected $description = 'Delete audit log rows older than the retention period (idempotent)';

    public function handle(): int
    {
        $months = max(1, (int) $this->option('months'));

        $deleted = Activity::query()->withoutGlobalScopes()
            ->where('created_at', '<', now()->subMonths($months))
            ->delete();

        $this->info("Pruned {$deleted} audit rows older than {$months} months.");

        return self::SUCCESS;
    }
}
