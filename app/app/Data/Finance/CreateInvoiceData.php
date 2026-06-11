<?php

declare(strict_types=1);

namespace App\Data\Finance;

use Spatie\LaravelData\Data;

class CreateInvoiceData extends Data
{
    /** @param list<array{description: string, quantity: float|int, unit_price_cents: int}> $lines */
    public function __construct(
        public readonly string $customer_id,
        public readonly string $issue_date,
        public readonly array $lines,
        public readonly ?string $due_date = null, // defaults to customer payment terms
        public readonly ?string $notes = null,
        public readonly ?string $source_deal_id = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'string'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'lines.*.unit_price_cents' => ['required', 'integer'],
        ];
    }
}
