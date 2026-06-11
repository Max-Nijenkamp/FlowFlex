<?php

declare(strict_types=1);

namespace App\Support\Traits;

use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Applies tenant isolation to a model:
 *  1. registers CompanyScope (queries filter by current company)
 *  2. auto-fills company_id on create from CompanyContext
 *  3. provides the company() relation
 *
 * @phpstan-require-extends Model
 */
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model): void {
            if (! $model->company_id && app(CompanyContext::class)->has()) {
                $model->company_id = app(CompanyContext::class)->current()->id;
            }
        });
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
