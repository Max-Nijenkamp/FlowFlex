<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\CreatePayrollRunData;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\PayrollEntry;
use App\Models\HR\PayrollRun;
use App\Models\User;

interface PayrollServiceInterface
{
    public function createRun(CreatePayrollRunData $data, Company $company): PayrollRun;

    public function addEmployee(
        PayrollRun $run,
        Employee $employee,
        float $grossPay,
        float $netPay,
        array $deductions = [],
        array $additions = [],
        ?string $notes = null,
    ): PayrollEntry;

    public function calculateTotals(PayrollRun $run): PayrollRun;

    public function approve(PayrollRun $run, User $approver): PayrollRun;
}
