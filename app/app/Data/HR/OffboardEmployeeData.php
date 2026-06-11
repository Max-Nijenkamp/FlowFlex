<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class OffboardEmployeeData extends Data
{
    public function __construct(
        public readonly string $employee_id,
        public readonly string $termination_date,
        public readonly string $termination_reason,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'string'],
            'termination_date' => ['required', 'date'],
            'termination_reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
