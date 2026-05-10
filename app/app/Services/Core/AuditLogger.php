<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\ActivityLogger;

class AuditLogger
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function log(
        string $description,
        string $event = 'custom',
        ?Model $subject = null,
        array $properties = [],
    ): void {
        $logger = activity('audit')
            ->withProperties(array_merge(['event' => $event], $properties))
            ->event($event);

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        if (auth()->check()) {
            $logger->causedBy(auth()->user());
        }

        $this->tapCompanyId($logger);

        $logger->log($description);
    }

    private function tapCompanyId(ActivityLogger $logger): void
    {
        $company = $this->companyContext->hasCompany() ? $this->companyContext->current() : null;
        if ($company === null) {
            return;
        }

        $logger->tap(function (\Spatie\Activitylog\Models\Activity $activity) use ($company): void {
            $activity->company_id = $company->id;
            $activity->ip_address = request()->ip();
            $activity->user_agent = request()->userAgent();
        });
    }
}
