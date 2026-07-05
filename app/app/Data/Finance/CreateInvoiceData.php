<?php

declare(strict_types=1);

namespace App\Data\Finance;

use Spatie\LaravelData\Data;

class CreateInvoiceData extends Data
{
    /** @param  list<array{description: string, quantity: float|string, unit_price_cents: int, tax_rate_percent?: float|string}>  $lines */
    public function __construct(
        public string $customerId,
        public array $lines,
        public ?string $issueDate = null,
        public ?string $dueDate = null,
        public float $discountPercent = 0,
        public ?string $notes = null,
        public ?string $recurringSchedule = null,
        public ?string $sourceDealId = null,
    ) {}
}
