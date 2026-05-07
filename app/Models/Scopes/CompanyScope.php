<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to the given query.
     *
     * In HTTP / Filament context the tenant guard is active and company_id is
     * automatically scoped to the authenticated tenant's company.
     *
     * In queued job / console context the tenant guard is NOT active, so this
     * scope applies nothing. Jobs MUST use one of these explicit patterns to
     * avoid cross-company data leaks:
     *
     *   Model::forCompany($companyId)->...   (defined in BelongsToCompany trait)
     *   Model::withoutGlobalScopes()->where('company_id', $companyId)->...
     *
     * Never call Model::all() or Model::query() without company context in jobs.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth('tenant')->check()) {
            $builder->where(
                $model->getTable() . '.company_id',
                auth('tenant')->user()->company_id
            );
        }
        // No else-branch: in job/console context the scope intentionally does
        // nothing. Jobs are responsible for scoping to a company explicitly.
    }
}
