<?php

namespace App\Jobs\Hr;

use App\Events\Hr\PayslipGenerated;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
use App\Models\Hr\Payslip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayslipPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly PayRun $payRun,
        public readonly Employee $employee,
    ) {}

    public function handle(): void
    {
        // Find or create payslip record.
        $payslip = Payslip::firstOrCreate(
            [
                'pay_run_id'  => $this->payRun->id,
                'employee_id' => $this->employee->id,
            ],
            [
                'company_id'   => $this->payRun->company_id,
                'period_start' => $this->payRun->pay_period_start,
                'period_end'   => $this->payRun->pay_period_end,
                'status'       => 'generated',
            ]
        );

        // PDF generation placeholder — integrate with Laravel PDF package later.
        // $pdf = Pdf::loadView('payslips.pdf', ['payslip' => $payslip, 'employee' => $this->employee]);
        // $path = "payslips/{$this->payRun->id}/{$this->employee->id}.pdf";
        // Storage::put($path, $pdf->output());
        // $payslip->update(['pdf_path' => $path]);

        event(new PayslipGenerated($payslip));
    }
}
