<?php

namespace App\Listeners\Hr;

use App\Events\Hr\PayslipGenerated;
use App\Models\Tenant;
use App\Notifications\Hr\PayslipGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmployeePayslipGenerated implements ShouldQueue
{
    public function handle(PayslipGenerated $event): void
    {
        $payslip = $event->payslip;
        $employee = $payslip->employee;

        if (! $employee || ! $employee->email) {
            return;
        }

        $tenant = Tenant::where('company_id', $payslip->company_id)
            ->where('email', $employee->email)
            ->first();

        if ($tenant) {
            $tenant->notify(new PayslipGeneratedNotification($payslip));
        }
    }
}
