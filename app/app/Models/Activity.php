<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * Tenant-scoped activity log row. ULID PK + company_id via BelongsToCompany.
 * All writes go through AuditLogger::log() — never activity() directly.
 *
 * @property string|null $company_id
 */
class Activity extends SpatieActivity
{
    use BelongsToCompany, HasUlids;
}
