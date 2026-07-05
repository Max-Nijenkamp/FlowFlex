<?php

declare(strict_types=1);

namespace App\Data\Hr;

use Spatie\LaravelData\Data;

class CreateEmployeeData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $jobTitle,
        public string $hireDate,
        public string $employmentType = 'full-time',
        public ?string $phone = null,
        public ?string $personalEmail = null,
        public ?string $dateOfBirth = null,
        public ?string $nationalId = null,
        public ?string $departmentId = null,
        public ?string $managerId = null,
        public ?string $userId = null,
    ) {}
}
