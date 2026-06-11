<?php

declare(strict_types=1);

namespace App\Data\Core;

use App\Models\Core\BillingInvoice;
use Brick\Money\Money;
use Spatie\LaravelData\Data;

class BillingInvoiceData extends Data
{
    /** @param list<array<string, mixed>> $lines */
    public function __construct(
        public readonly string $id,
        public readonly string $period_start,
        public readonly string $period_end,
        public readonly int $total_cents,
        public readonly string $currency,
        public readonly string $total_formatted,
        public readonly string $status,
        public readonly ?string $paid_at,
        public readonly array $lines,
    ) {}

    public static function fromModel(BillingInvoice $invoice): self
    {
        return new self(
            id: $invoice->id,
            period_start: $invoice->period_start->toDateString(),
            period_end: $invoice->period_end->toDateString(),
            total_cents: $invoice->total_cents,
            currency: $invoice->currency,
            total_formatted: (string) Money::ofMinor($invoice->total_cents, $invoice->currency),
            status: (string) $invoice->status,
            paid_at: $invoice->paid_at?->toIso8601String(),
            lines: $invoice->lines->map(fn ($line) => [
                'module_key' => $line->module_key,
                'module_name' => $line->module_name,
                'user_count' => $line->user_count,
                'unit_price_cents' => $line->unit_price_cents,
                'line_total_cents' => $line->line_total_cents,
            ])->all(),
        );
    }
}
