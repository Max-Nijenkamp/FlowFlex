<?php

declare(strict_types=1);

namespace App\Data\Finance;

use Spatie\LaravelData\Data;

class SubmitExpenseData extends Data
{
    public function __construct(
        public readonly string $category_id,
        public readonly int $amount_cents,
        public readonly string $expense_date,
        public readonly string $merchant,
        public readonly ?string $description = null,
        public readonly ?string $employee_id = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'category_id' => ['required', 'string'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'merchant' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
