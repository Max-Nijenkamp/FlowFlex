<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\OffboardEmployeeData;
use App\Events\HR\EmployeeHired;
use App\Events\HR\EmployeeOffboarded;
use App\Exceptions\HR\ManagerCycleException;
use App\Models\HR\Employee;
use App\States\HR\Employee\Terminated;
use App\Support\Services\CompanyContext;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeService implements EmployeeServiceInterface
{
    public function hire(CreateEmployeeData $data): Employee
    {
        if (Employee::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An employee with this email already exists in your company.',
            ]);
        }

        if ($data->manager_id !== null) {
            Employee::query()->findOrFail($data->manager_id); // must exist in company
        }

        $employee = DB::transaction(function () use ($data): Employee {
            return Employee::create([
                'employee_number' => $this->nextEmployeeNumber(),
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'phone' => $data->phone !== null ? phone($data->phone)->formatE164() : null,
                'personal_email' => $data->personal_email,
                'date_of_birth' => $data->date_of_birth,
                'birth_year' => $data->date_of_birth !== null ? (int) date('Y', strtotime($data->date_of_birth)) : null,
                'national_id' => $data->national_id,
                'national_id_hash' => $data->national_id !== null ? hash('sha256', $data->national_id) : null,
                'hire_date' => $data->hire_date,
                'job_title' => $data->job_title,
                'department_id' => $data->department_id,
                'manager_id' => $data->manager_id,
                'employment_type' => $data->employment_type,
            ]);
        });

        event(new EmployeeHired(
            company_id: $employee->company_id,
            employee_id: $employee->id,
            user_id: $employee->user_id,
            start_date: CarbonImmutable::parse($employee->hire_date),
            job_title: $employee->job_title,
        ));

        return $employee;
    }

    public function offboard(OffboardEmployeeData $data): Employee
    {
        $employee = Employee::query()->findOrFail($data->employee_id);

        $employee->forceFill([
            'termination_date' => $data->termination_date,
            'termination_reason' => $data->termination_reason,
        ])->save();

        $employee->status->transitionTo(Terminated::class);

        event(new EmployeeOffboarded(
            company_id: $employee->company_id,
            employee_id: $employee->id,
            user_id: $employee->user_id,
            termination_date: CarbonImmutable::parse($data->termination_date),
        ));

        return $employee->refresh();
    }

    public function directReports(string $employeeId): Collection
    {
        return Employee::query()->where('manager_id', $employeeId)->get();
    }

    public function assignManager(string $employeeId, ?string $managerId): Employee
    {
        $employee = Employee::query()->findOrFail($employeeId);

        if ($managerId !== null) {
            $this->assertNoCycle($employeeId, $managerId);
        }

        $employee->update(['manager_id' => $managerId]);

        return $employee->refresh();
    }

    private function assertNoCycle(string $employeeId, string $managerId): void
    {
        $current = $managerId;
        $seen = [];

        while ($current !== null) {
            if ($current === $employeeId || isset($seen[$current])) {
                throw new ManagerCycleException('This manager assignment would create a reporting cycle.');
            }
            $seen[$current] = true;
            $current = Employee::query()->whereKey($current)->value('manager_id');
        }
    }

    private function nextEmployeeNumber(): string
    {
        $companyId = app(CompanyContext::class)->current()->id;

        // Advisory max+1 inside the surrounding transaction; unique index backs races.
        $max = (int) Employee::query()
            ->withTrashed()
            ->selectRaw('MAX(CAST(employee_number AS INTEGER)) as m')
            ->value('m');

        return (string) ($max + 1);
    }
}
