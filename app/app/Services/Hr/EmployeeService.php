<?php

declare(strict_types=1);

namespace App\Services\Hr;

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\CreateEmployeeData;
use App\Data\Hr\OffboardEmployeeData;
use App\Events\Hr\EmployeeHired;
use App\Events\Hr\EmployeeOffboarded;
use App\Exceptions\Hr\ManagerCycleException;
use App\Models\Hr\Employee;
use App\Models\User;
use App\States\Hr\Employee\Terminated;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Owns hr_employees writes (hr.profiles). Sequential per-company
 * employee numbers under lock; phone normalised to E.164; encrypted
 * PII with hash/derived lookup columns; manager cycles rejected.
 */
class EmployeeService implements EmployeeServiceInterface
{
    public function hire(CreateEmployeeData $data): Employee
    {
        $companyId = app(CompanyContext::class)->current()->id;

        if (Employee::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An employee with this work email already exists.',
            ]);
        }

        $employee = DB::transaction(function () use ($data, $companyId): Employee {
            return Employee::query()->create([
                'company_id' => $companyId,
                'user_id' => $data->userId,
                'employee_number' => $this->nextEmployeeNumber(),
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'email' => $data->email,
                'phone' => self::normalisePhone($data->phone),
                'personal_email' => $data->personalEmail,
                'date_of_birth' => $data->dateOfBirth,
                'birth_year' => $data->dateOfBirth !== null ? (int) now()->parse($data->dateOfBirth)->format('Y') : null,
                'national_id' => $data->nationalId,
                'national_id_hash' => $data->nationalId !== null ? hash('sha256', $data->nationalId) : null,
                'hire_date' => $data->hireDate,
                'job_title' => $data->jobTitle,
                'department_id' => $data->departmentId,
                'manager_id' => $data->managerId,
                'employment_type' => $data->employmentType,
            ]);
        });

        EmployeeHired::dispatch(
            $employee->company_id,
            $employee->id,
            $employee->user_id,
            $employee->hire_date->toDateString(),
            $employee->job_title,
        );

        $causer = Auth::user();
        app(AuditLogger::class)->log(
            'hr.employee-hired',
            $employee,
            $causer instanceof User ? $causer : null,
            ['employee_number' => $employee->employee_number, 'job_title' => $employee->job_title],
        );

        return $employee;
    }

    public function changeManager(string $employeeId, ?string $managerId): Employee
    {
        /** @var Employee $employee */
        $employee = Employee::query()->findOrFail($employeeId);

        if ($managerId !== null) {
            self::assertNoCycle($employee->id, $managerId);
        }

        $employee->update(['manager_id' => $managerId]);

        return $employee->refresh();
    }

    public function offboard(OffboardEmployeeData $data): Employee
    {
        if (trim($data->reason) === '') {
            throw ValidationException::withMessages(['reason' => 'A termination reason is required.']);
        }

        /** @var Employee $employee */
        $employee = Employee::query()->findOrFail($data->employeeId);

        $employee->status->transitionTo(Terminated::class);
        $employee->update([
            'termination_date' => $data->terminationDate,
            'termination_reason' => $data->reason,
        ]);

        EmployeeOffboarded::dispatch(
            $employee->company_id,
            $employee->id,
            $employee->user_id,
            $data->terminationDate,
            $data->reason,
        );

        $causer = Auth::user();
        app(AuditLogger::class)->log(
            'hr.employee-offboarded',
            $employee,
            $causer instanceof User ? $causer : null,
            ['termination_date' => $data->terminationDate, 'reason' => $data->reason],
        );

        return $employee->refresh();
    }

    /** Walk up from the proposed manager — hitting the employee = cycle. */
    public static function assertNoCycle(string $employeeId, string $managerId): void
    {
        if ($employeeId === $managerId) {
            throw ManagerCycleException::make();
        }

        $current = $managerId;
        $hops = 0;

        while ($current !== null && $hops < 100) {
            if ($current === $employeeId) {
                throw ManagerCycleException::make();
            }

            $current = Employee::query()->whereKey($current)->value('manager_id');
            $hops++;
        }
    }

    public static function normalisePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        try {
            return (string) phone($phone, 'NL')->formatE164();
        } catch (\Throwable) {
            return $phone; // keep the raw value rather than lose it
        }
    }

    /** EMP-0001-style sequence per company; race-safe under lock. */
    private function nextEmployeeNumber(): string
    {
        $last = Employee::query()
            ->lockForUpdate()
            ->orderByDesc('employee_number')
            ->value('employee_number');

        $next = $last === null ? 1 : ((int) substr((string) $last, 4)) + 1;

        return 'EMP-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
