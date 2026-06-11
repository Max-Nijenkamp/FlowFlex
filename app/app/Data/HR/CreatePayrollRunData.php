<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class CreatePayrollRunData extends Data
{
    /** @param list<string> $employee_ids */
    public function __construct(
        public readonly string $period_start,
        public readonly string $period_end,
        public readonly array $employee_ids,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['string'],
        ];
    }
}
