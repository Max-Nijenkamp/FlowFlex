<?php

declare(strict_types=1);

namespace App\Console\Commands\Hr;

use App\Models\Company;
use App\Services\Hr\LeaveService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Console\Command;

/**
 * Annual accrual + carry-over run (hr.leave/accrual-jobs). Idempotent:
 * allocations are recomputed, never incremented — a re-run converges on
 * the same numbers.
 */
class RunLeaveAccrualCommand extends Command
{
    protected $signature = 'hr:run-leave-accrual {--year=}';

    protected $description = 'Allocate annual leave (accrual + carry-over) for every company';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now()->year);

        $companies = Company::query()->withoutGlobalScopes()->whereNull('deleted_at')->get();
        $total = 0;

        foreach ($companies as $company) {
            WithCompanyContext::restore($company->id);
            $total += app(LeaveService::class)->runAccrual($company->id, $year);
        }

        app(CompanyContext::class)->forget();

        $this->info("Balances allocated for {$year}: {$total}");

        return self::SUCCESS;
    }
}
