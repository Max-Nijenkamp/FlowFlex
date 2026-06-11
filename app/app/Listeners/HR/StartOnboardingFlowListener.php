<?php

declare(strict_types=1);

namespace App\Listeners\HR;

use App\Contracts\HR\OnboardingServiceInterface;
use App\Events\HR\EmployeeHired;
use App\Models\Company;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StartOnboardingFlowListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(EmployeeHired $event): void
    {
        $company = Company::query()->withoutGlobalScopes()->findOrFail($event->company_id);
        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // Default plan if a template exists; no-op otherwise (event-bus contract).
        app(OnboardingServiceInterface::class)->startPlan($event->company_id, $event->employee_id);
    }
}
