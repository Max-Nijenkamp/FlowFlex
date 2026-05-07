<?php

namespace App\Policies\Finance;

use App\Models\Finance\ExpenseCategory;
use App\Models\Tenant;

class ExpenseCategoryPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.expense-categories.view');
    }

    public function view(Tenant $tenant, ExpenseCategory $expenseCategory): bool
    {
        return $tenant->company_id === $expenseCategory->company_id
            && $tenant->can('finance.expense-categories.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.expense-categories.create');
    }

    public function update(Tenant $tenant, ExpenseCategory $expenseCategory): bool
    {
        return $tenant->company_id === $expenseCategory->company_id
            && $tenant->can('finance.expense-categories.edit');
    }

    public function delete(Tenant $tenant, ExpenseCategory $expenseCategory): bool
    {
        return $tenant->company_id === $expenseCategory->company_id
            && $tenant->can('finance.expense-categories.delete');
    }

    public function restore(Tenant $tenant, ExpenseCategory $expenseCategory): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, ExpenseCategory $expenseCategory): bool
    {
        return false;
    }
}
