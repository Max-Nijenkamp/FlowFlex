<?php

declare(strict_types=1);

namespace App\Data\HR;

use App\Models\HR\Employee;
use Spatie\LaravelData\Data;

class EmployeeData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $employee_number,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $full_name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $job_title,
        public readonly ?string $department_id,
        public readonly ?string $manager_id,
        public readonly string $employment_type,
        public readonly string $status,
        public readonly string $hire_date,
        public readonly ?string $termination_date,
    ) {}

    public static function fromModel(Employee $employee): self
    {
        return new self(
            id: $employee->id,
            employee_number: $employee->employee_number,
            first_name: $employee->first_name,
            last_name: $employee->last_name,
            full_name: $employee->full_name,
            email: $employee->email,
            phone: $employee->phone,
            job_title: $employee->job_title,
            department_id: $employee->department_id,
            manager_id: $employee->manager_id,
            employment_type: $employee->employment_type,
            status: (string) $employee->status,
            hire_date: $employee->hire_date->toDateString(),
            termination_date: $employee->termination_date?->toDateString(),
        );
    }
}
