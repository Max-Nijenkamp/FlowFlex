<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class CreateEmployeeData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $hire_date,
        public readonly string $job_title,
        public readonly string $employment_type,
        public readonly ?string $phone = null,
        public readonly ?string $personal_email = null,
        public readonly ?string $date_of_birth = null,
        public readonly ?string $national_id = null,
        public readonly ?string $department_id = null,
        public readonly ?string $manager_id = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'phone:AUTO'],
            'personal_email' => ['nullable', 'email'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'hire_date' => ['required', 'date'],
            'job_title' => ['required', 'string', 'max:150'],
            'department_id' => ['nullable', 'string'],
            'manager_id' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full-time,part-time,contractor'],
        ];
    }
}
