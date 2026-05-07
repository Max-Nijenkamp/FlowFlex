<?php

namespace App\Concerns;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function (self $model): void {
            if (auth('tenant')->check() && empty($model->company_id)) {
                // HTTP / Filament context: use authenticated tenant's company.
                $model->company_id = auth('tenant')->user()->company_id;
            } elseif (empty($model->company_id)) {
                // API context: AuthenticateApiKey middleware stores a Company model
                // instance in Symfony request attributes (not query/body parameters).
                $apiCompany = request()->attributes->get('api_company');
                if ($apiCompany && isset($apiCompany->id)) {
                    $model->company_id = $apiCompany->id;
                }
            }
        });
    }

    /**
     * Return a query scoped to a specific company, bypassing the global scope.
     *
     * Use this in queued jobs and console commands instead of a bare query():
     *
     *   Employee::forCompany($companyId)->where(...)->get();
     */
    public static function forCompany(string $companyId): Builder
    {
        return static::withoutGlobalScopes()->where('company_id', $companyId);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
