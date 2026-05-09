<?php

declare(strict_types=1);

namespace App\Support\Traits;

use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (! $model->company_id) {
                $model->company_id = app(CompanyContext::class)->currentId();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
