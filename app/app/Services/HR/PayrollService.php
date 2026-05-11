<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\PayrollServiceInterface;
use App\Data\HR\CreatePayrollRunData;
use App\Events\HR\PayrollRunApproved;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\PayrollEntry;
use App\Models\HR\PayrollRun;
use App\Models\User;

class PayrollService implements PayrollServiceInterface
{
    public function createRun(CreatePayrollRunData $data, Company $company): PayrollRun
    {
        return PayrollRun::withoutGlobalScopes()->create([
            'company_id'  => $company->id,
            'name'        => $data->name,
            'period_start' => $data->period_start,
            'period_end'  => $data->period_end,
            'pay_date'    => $data->pay_date,
            'currency'    => $data->currency,
            'status'      => 'draft',
        ]);
    }

    public function addEmployee(
        PayrollRun $run,
        Employee $employee,
        float $grossPay,
        float $netPay,
        array $deductions = [],
        array $additions = [],
        ?string $notes = null,
    ): PayrollEntry {
        return PayrollEntry::withoutGlobalScopes()->create([
            'company_id'  => $run->company_id,
            'run_id'      => $run->id,
            'employee_id' => $employee->id,
            'gross_pay'   => $grossPay,
            'net_pay'     => $netPay,
            'deductions'  => $deductions,
            'additions'   => $additions,
            'notes'       => $notes,
        ]);
    }

    public function calculateTotals(PayrollRun $run): PayrollRun
    {
        $totals = $run->entries()->withoutGlobalScopes()
            ->selectRaw('SUM(gross_pay) as total_gross, SUM(net_pay) as total_net')
            ->first();

        $totalGross = (float) ($totals->total_gross ?? 0);
        $totalNet   = (float) ($totals->total_net ?? 0);

        $run->update([
            'total_gross'      => $totalGross,
            'total_net'        => $totalNet,
            'total_deductions' => $totalGross - $totalNet,
        ]);

        return $run->fresh();
    }

    public function approve(PayrollRun $run, User $approver): PayrollRun
    {
        $this->calculateTotals($run);

        $run->update([
            'status'      => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $company = $run->company()->withoutGlobalScopes()->first();
        event(new PayrollRunApproved($company, $run->fresh()));

        return $run->fresh();
    }
}
