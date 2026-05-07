<?php

namespace App\Policies\Finance;

use App\Models\Finance\Expense;
use App\Models\Tenant;

class ExpensePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.expenses.view');
    }

    public function view(Tenant $tenant, Expense $expense): bool
    {
        return $tenant->company_id === $expense->company_id
            && $tenant->can('finance.expenses.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.expenses.create');
    }

    public function update(Tenant $tenant, Expense $expense): bool
    {
        return $tenant->company_id === $expense->company_id
            && $tenant->can('finance.expenses.edit');
    }

    public function delete(Tenant $tenant, Expense $expense): bool
    {
        return $tenant->company_id === $expense->company_id
            && $tenant->can('finance.expenses.delete');
    }

    public function approve(Tenant $tenant, Expense $expense): bool
    {
        return $tenant->company_id === $expense->company_id
            && $tenant->can('finance.expenses.approve');
    }

    public function restore(Tenant $tenant, Expense $expense): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Expense $expense): bool
    {
        return false;
    }
}
