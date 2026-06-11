<?php

declare(strict_types=1);

namespace App\Data\Finance;

use App\Models\Finance\Invoice;
use Spatie\LaravelData\Data;

class InvoiceData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $invoice_number,
        public readonly string $customer_id,
        public readonly string $status,
        public readonly string $issue_date,
        public readonly string $due_date,
        public readonly int $subtotal_cents,
        public readonly int $total_cents,
        public readonly int $paid_amount_cents,
        public readonly string $currency,
    ) {}

    public static function fromModel(Invoice $invoice): self
    {
        return new self(
            id: $invoice->id,
            invoice_number: $invoice->invoice_number,
            customer_id: $invoice->customer_id,
            status: (string) $invoice->status,
            issue_date: $invoice->issue_date->toDateString(),
            due_date: $invoice->due_date->toDateString(),
            subtotal_cents: $invoice->subtotal_cents,
            total_cents: $invoice->total_cents,
            paid_amount_cents: $invoice->paid_amount_cents,
            currency: $invoice->currency,
        );
    }
}
