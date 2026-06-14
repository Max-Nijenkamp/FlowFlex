<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Audit logging for tenant models (core.audit). Log name = domain prefix of
 * the table (hr_employees → "hr"), so feeds and filters can color by domain.
 * v5 namespaces are non-obvious — see vault memory: Models\Concerns +
 * Support\LogOptions, NOT Traits\LogsActivity.
 */
trait LogsCompanyActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName(explode('_', $this->getTable())[0])
            ->setDescriptionForEvent(fn (string $eventName): string => str(class_basename($this))->headline()->lower()." {$eventName}");
    }
}
