<?php

namespace App\Listeners\Hr;

use App\Events\Hr\PayRunProcessed;
use App\Jobs\Hr\GeneratePayslipPdf;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchPayslipGenerationJobs implements ShouldQueue
{
    public function handle(PayRunProcessed $event): void
    {
        // Eager-load runEmployees with their employee in one query to avoid N+1.
        $payRun = $event->payRun->load('runEmployees.employee');

        foreach ($payRun->runEmployees as $runEmployee) {
            $employee = $runEmployee->employee;

            if ($employee) {
                GeneratePayslipPdf::dispatch($payRun, $employee);
            }
        }
    }
}
