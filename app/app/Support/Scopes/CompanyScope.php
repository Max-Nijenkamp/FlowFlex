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
        $context = app(CompanyContext::class);

        if ($context->hasCompany()) {
            $builder->where($model->getTable() . '.company_id', $context->id());
        }
    }
}
