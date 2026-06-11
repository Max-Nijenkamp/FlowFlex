<?php

declare(strict_types=1);

namespace App\Events\Finance;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class InvoicePaid
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $invoice_id,
        public readonly ?string $crm_account_id,
        public readonly int $amount_cents,
        public readonly string $currency,
        public readonly CarbonImmutable $paid_at,
    ) {}
}
