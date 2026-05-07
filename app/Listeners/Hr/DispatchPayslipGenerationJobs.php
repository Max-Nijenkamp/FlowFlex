<?php

namespace App\Listeners\Hr;

use App\Events\Hr\PayRunProcessed;
use App\Jobs\Hr\GeneratePayslipPdf;
use App\Models\Hr\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchPayslipGenerationJobs implements ShouldQueue
{
    public function handle(PayRunProcessed $event): void
    {
        $payRun = $event->payRun;

        foreach ($payRun->runEmployees as $runEmployee) {
            $employee = Employee::find($runEmployee->employee_id);

            if ($employee) {
                GeneratePayslipPdf::dispatch($payRun, $employee);
            }
        }
    }
}
