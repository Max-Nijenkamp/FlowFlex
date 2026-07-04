<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Company;
use Illuminate\Console\Command;

/**
 * Daily 04:30 retention prune (core.audit-log): hard-deletes rows older than
 * each company's retention cutoff (companies.audit_retention_days, default
 * 730 days). Delete-only WHERE created_at < cutoff — naturally idempotent.
 */
class PruneAuditLogCommand extends Command
{
    public const DEFAULT_RETENTION_DAYS = 730;

    protected $signature = 'audit:prune';

    protected $description = 'Prune audit log rows past each company\'s retention window';

    public function handle(): int
    {
        $total = 0;

        Company::query()->eachById(function (Company $company) use (&$total): void {
            $days = $company->audit_retention_days ?? self::DEFAULT_RETENTION_DAYS;

            $total += Activity::query()
                ->withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('created_at', '<', now()->subDays($days))
                ->forceDelete();
        });

        $this->info("Pruned {$total} audit rows.");

        return self::SUCCESS;
    }
}
