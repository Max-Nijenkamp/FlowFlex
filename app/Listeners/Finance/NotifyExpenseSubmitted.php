<?php

namespace App\Listeners\Finance;

use App\Events\Finance\ExpenseSubmitted;
use App\Models\Tenant;
use App\Notifications\Finance\ExpenseSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyExpenseSubmitted implements ShouldQueue
{
    public function handle(ExpenseSubmitted $event): void
    {
        $expense = $event->expense;

        // Notify tenants with expense approval permission (finance team)
        Tenant::where('company_id', $expense->company_id)
            ->get()
            ->filter(fn (Tenant $tenant) => $tenant->can('finance.expenses.approve'))
            ->each(fn (Tenant $tenant) => $tenant->notify(new ExpenseSubmittedNotification($expense)));
    }
}
