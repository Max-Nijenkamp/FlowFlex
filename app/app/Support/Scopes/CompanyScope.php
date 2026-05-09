<?php

declare(strict_types=1);

namespace App\Support\Scopes;

use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($companyId = app(CompanyContext::class)->currentId()) {
            $builder->where($model->getTable() . '.company_id', $companyId);
        }
    }
}
