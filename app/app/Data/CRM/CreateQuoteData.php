<?php

declare(strict_types=1);

namespace App\Data\CRM;

use Spatie\LaravelData\Data;

class CreateQuoteData extends Data
{
    /** @param list<array{description: string, quantity: float|int, unit_price_cents: int}> $lines */
    public function __construct(
        public readonly array $lines,
        public readonly ?string $deal_id = null,
        public readonly ?string $contact_id = null,
        public readonly ?string $valid_until = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'lines.*.unit_price_cents' => ['required', 'integer'],
            'valid_until' => ['nullable', 'date', 'after:today'],
        ];
    }
}
