<?php

declare(strict_types=1);

namespace App\Support\Traits;

use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (empty($model->company_id) && app(CompanyContext::class)->hasCompany()) {
                $model->company_id = app(CompanyContext::class)->id();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
