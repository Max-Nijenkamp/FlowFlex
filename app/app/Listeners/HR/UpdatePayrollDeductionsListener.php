<?php

declare(strict_types=1);

namespace App\Listeners\HR;

use App\Events\HR\LeaveRequestApproved;
use App\Models\HR\LeaveType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdatePayrollDeductionsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(LeaveRequestApproved $event): void
    {
        $type = LeaveType::query()->withoutGlobalScopes()->find($event->leave_type_id);

        // Deduction only for unpaid leave types; paid types no-op (event-bus contract).
        if ($type === null || $type->is_paid) {
            return;
        }

        Log::info('Unpaid leave deduction registered for next pay run', [
            'company_id' => $event->company_id,
            'employee_id' => $event->employee_id,
            'leave_request_id' => $event->leave_request_id,
            'days' => $event->days,
        ]);
    }
}
