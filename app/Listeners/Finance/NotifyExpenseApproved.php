<?php

namespace App\Listeners\Finance;

use App\Events\Finance\ExpenseApproved;
use App\Models\Tenant;
use App\Notifications\Finance\ExpenseApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyExpenseApproved implements ShouldQueue
{
    public function handle(ExpenseApproved $event): void
    {
        $expense = $event->expense;

        // Notify the expense submitter
        $submitter = Tenant::find($expense->tenant_id);
        $submitter?->notify(new ExpenseApprovedNotification($expense));
    }
}
