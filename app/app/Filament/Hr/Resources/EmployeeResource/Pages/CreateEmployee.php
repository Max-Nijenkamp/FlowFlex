<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\EmployeeResource\Pages;

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\CreateEmployeeData;
use App\Filament\Hr\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    /**
     * Hiring runs through EmployeeService — number sequence, E.164
     * normalisation, encrypted-field derivations and the EmployeeHired
     * event all live there, not in the form.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            jobTitle: $data['job_title'],
            hireDate: $data['hire_date'],
            employmentType: $data['employment_type'],
            phone: $data['phone'] ?? null,
            personalEmail: $data['personal_email'] ?? null,
            dateOfBirth: $data['date_of_birth'] ?? null,
            nationalId: $data['national_id'] ?? null,
            departmentId: $data['department_id'] ?? null,
            managerId: $data['manager_id'] ?? null,
            userId: $data['user_id'] ?? null,
        ));
    }
}
