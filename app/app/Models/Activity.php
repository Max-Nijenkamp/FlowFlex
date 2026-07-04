<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * Tenant-scoped activity row. company_id is force-set by BelongsToCompany's
 * creating hook from CompanyContext — never client-supplied. Append-only:
 * all writes go through AuditLogger, reads through the log browsers.
 *
 * @property string $id
 * @property string $company_id
 */
class Activity extends SpatieActivity
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;
}
