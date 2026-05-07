<?php

namespace App\Listeners\Finance;

use App\Events\Finance\ExpenseRejected;
use App\Models\Tenant;
use App\Notifications\Finance\ExpenseRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyExpenseRejected implements ShouldQueue
{
    public function handle(ExpenseRejected $event): void
    {
        $expense = $event->expense;

        // Notify the expense submitter
        $submitter = Tenant::find($expense->tenant_id);
        $submitter?->notify(new ExpenseRejectedNotification($expense));
    }
}
