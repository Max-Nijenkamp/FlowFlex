<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class SubmitLeaveRequestData extends Data
{
    public function __construct(
        public readonly string $employee_id,
        public readonly string $leave_type_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly ?string $note = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'string'],
            'leave_type_id' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public static function messages(): array
    {
        return ['end_date.after_or_equal' => 'End date must be on or after start date.'];
    }
}
