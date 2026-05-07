<?php

namespace App\Policies\Finance;

use App\Models\Finance\ExpenseReport;
use App\Models\Tenant;

class ExpenseReportPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.expense-reports.view');
    }

    public function view(Tenant $tenant, ExpenseReport $report): bool
    {
        return $tenant->company_id === $report->company_id
            && $tenant->can('finance.expense-reports.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.expense-reports.create');
    }

    public function update(Tenant $tenant, ExpenseReport $report): bool
    {
        return $tenant->company_id === $report->company_id
            && $tenant->can('finance.expense-reports.edit');
    }

    public function delete(Tenant $tenant, ExpenseReport $report): bool
    {
        return $tenant->company_id === $report->company_id
            && $tenant->can('finance.expense-reports.delete');
    }

    public function approve(Tenant $tenant, ExpenseReport $report): bool
    {
        return $tenant->company_id === $report->company_id
            && $tenant->can('finance.expense-reports.approve');
    }

    public function restore(Tenant $tenant, ExpenseReport $report): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, ExpenseReport $report): bool
    {
        return false;
    }
}
