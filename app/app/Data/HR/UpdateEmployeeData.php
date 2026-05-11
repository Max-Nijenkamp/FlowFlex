<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class UpdateEmployeeData extends Data
{
    public function __construct(
        public readonly ?string $first_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $date_of_birth = null,
        public readonly ?string $hire_date = null,
        public readonly ?string $employment_type = null,
        public readonly ?string $department = null,
        public readonly ?string $job_title = null,
        public readonly ?string $manager_id = null,
        public readonly ?string $location = null,
        public readonly ?string $status = null,
        public readonly ?string $avatar_path = null,
        public readonly ?string $emergency_contact_name = null,
        public readonly ?string $emergency_contact_phone = null,
        public readonly ?array $custom_fields = null,
    ) {}
}
