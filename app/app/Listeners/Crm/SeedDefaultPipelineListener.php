<?php

declare(strict_types=1);

namespace App\Listeners\Crm;

use App\Events\ModuleActivated;
use App\Services\Crm\PipelineService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Activating crm.deals (the marketplace module that carries the board)
 * seeds the default pipeline so the board never opens empty-configured.
 */
class SeedDefaultPipelineListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(ModuleActivated $event): void
    {
        if (! in_array($event->module_key, ['crm.deals', 'crm.pipeline'], true)) {
            return;
        }

        WithCompanyContext::restore($event->company_id);

        PipelineService::ensureDefaultStages(app(CompanyContext::class)->current());
    }
}
