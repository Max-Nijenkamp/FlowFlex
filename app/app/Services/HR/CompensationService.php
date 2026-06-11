<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\CompensationBand;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeBenefit;
use App\Models\HR\PayrollEmployee;
use App\Models\HR\SalaryHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompensationService
{
    /** Updates the payroll profile salary + appends a history row — one transaction. */
    public function adjustSalary(string $employeeId, int $newSalaryCents, string $reason, string $effectiveDate): SalaryHistory
    {
        return DB::transaction(function () use ($employeeId, $newSalaryCents, $reason, $effectiveDate): SalaryHistory {
            PayrollEmployee::query()->updateOrCreate(
                ['employee_id' => $employeeId],
                ['salary_raw' => (string) $newSalaryCents, 'status' => 'ready'],
            );

            return SalaryHistory::create([
                'employee_id' => $employeeId,
                'amount_raw' => (string) $newSalaryCents,
                'salary_band' => $this->bandFor($newSalaryCents),
                'effective_date' => $effectiveDate,
                'reason' => $reason,
                'changed_by' => Auth::guard('web')->id(),
            ]);
        });
    }

    /** Salary position within the matching band: salary / mid. Null without a band. */
    public function compaRatio(string $employeeId): ?float
    {
        $employee = Employee::query()->findOrFail($employeeId);
        $salary = PayrollEmployee::query()->where('employee_id', $employeeId)->first()?->salaryCents();

        if ($salary === null) {
            return null;
        }

        $band = CompensationBand::query()
            ->where('job_grade', $employee->job_title)
            ->where(fn ($q) => $q->where('department_id', $employee->department_id)->orWhereNull('department_id'))
            ->orderByRaw('CASE WHEN department_id IS NOT NULL THEN 0 ELSE 1 END')
            ->first();

        if ($band === null || $band->mid_salary_cents === 0) {
            return null;
        }

        return round($salary / $band->mid_salary_cents, 2);
    }

    public function enroll(string $employeeId, string $benefitId): EmployeeBenefit
    {
        return EmployeeBenefit::query()->firstOrCreate(
            ['employee_id' => $employeeId, 'benefit_id' => $benefitId, 'unenrolled_at' => null],
            ['enrolled_at' => now()],
        );
    }

    public function unenroll(string $employeeBenefitId): void
    {
        EmployeeBenefit::query()->whereKey($employeeBenefitId)->update(['unenrolled_at' => now()]);
    }

    /** Coarse band label for reporting — value never reveals exact salary. */
    private function bandFor(int $cents): string
    {
        return match (true) {
            $cents < 250000 => '<2.5k',
            $cents < 400000 => '2.5k-4k',
            $cents < 600000 => '4k-6k',
            $cents < 850000 => '6k-8.5k',
            default => '8.5k+',
        };
    }
}
