<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * Tenant-scoped media row (core.file-storage): company_id force-set from
 * context by BelongsToCompany; CompanyPathGenerator prefixes every path
 * with companies/{company_id}/.
 *
 * @property string $id
 * @property string $company_id
 */
class Media extends SpatieMedia
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;
}
