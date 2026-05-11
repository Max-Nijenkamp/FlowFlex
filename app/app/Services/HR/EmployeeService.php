<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\UpdateEmployeeData;
use App\Events\HR\EmployeeHired;
use App\Events\HR\EmployeeTerminated;
use App\Models\Company;
use App\Models\HR\Employee;

class EmployeeService implements EmployeeServiceInterface
{
    public function create(CreateEmployeeData $data, Company $company): Employee
    {
        $employee = Employee::withoutGlobalScopes()->create([
            'company_id'               => $company->id,
            'user_id'                  => $data->user_id,
            'employee_number'          => $data->employee_number,
            'first_name'               => $data->first_name,
            'last_name'                => $data->last_name,
            'email'                    => $data->email,
            'phone'                    => $data->phone,
            'date_of_birth'            => $data->date_of_birth,
            'hire_date'                => $data->hire_date,
            'termination_date'         => $data->termination_date,
            'employment_type'          => $data->employment_type,
            'department'               => $data->department,
            'job_title'                => $data->job_title,
            'manager_id'               => $data->manager_id,
            'location'                 => $data->location,
            'status'                   => $data->status,
            'avatar_path'              => $data->avatar_path,
            'emergency_contact_name'   => $data->emergency_contact_name,
            'emergency_contact_phone'  => $data->emergency_contact_phone,
            'custom_fields'            => $data->custom_fields,
        ]);

        event(new EmployeeHired($company, $employee));

        return $employee;
    }

    public function update(Employee $employee, UpdateEmployeeData $data): Employee
    {
        // Build updates — filter out null values to avoid overwriting existing data
        // with unintended nulls when only a subset of fields is provided.
        $updates = array_filter([
            'first_name'               => $data->first_name,
            'last_name'                => $data->last_name,
            'email'                    => $data->email,
            'phone'                    => $data->phone,
            'date_of_birth'            => $data->date_of_birth,
            'hire_date'                => $data->hire_date,
            'employment_type'          => $data->employment_type,
            'department'               => $data->department,
            'job_title'                => $data->job_title,
            'location'                 => $data->location,
            'status'                   => $data->status,
            'avatar_path'              => $data->avatar_path,
            'emergency_contact_name'   => $data->emergency_contact_name,
            'emergency_contact_phone'  => $data->emergency_contact_phone,
            'custom_fields'            => $data->custom_fields,
        ], fn ($v) => $v !== null);

        // manager_id is a nullable FK that can be intentionally cleared to null.
        // Always include it so that passing manager_id: null correctly removes the
        // manager relationship rather than being silently dropped by array_filter.
        $updates['manager_id'] = $data->manager_id;

        $employee->update($updates);

        return $employee->fresh();
    }

    public function terminate(Employee $employee, ?string $terminationDate = null): Employee
    {
        $date = $terminationDate ?? now()->toDateString();

        $employee->update([
            'status'           => 'terminated',
            'termination_date' => $date,
        ]);

        $company = $employee->company()->withoutGlobalScopes()->first();

        event(new EmployeeTerminated($company, $employee->fresh()));

        return $employee->fresh();
    }
}
