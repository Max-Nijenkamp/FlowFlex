<?php

declare(strict_types=1);

namespace App\Support\Traits;

use App\Exceptions\CompanyMismatchException;
use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function (Model $model): void {
            $contextId = app(CompanyContext::class)->currentId();

            if (! $model->getAttribute('company_id')) {
                // Fail closed: no context and no explicit id -> throw, never a global row.
                $model->setAttribute('company_id', app(CompanyContext::class)->current()->id);
            } elseif ($contextId !== null && $model->getAttribute('company_id') !== $contextId) {
                // With a tenant context active, a differing explicit id is a forgery.
                throw new CompanyMismatchException;
            }
        });
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
