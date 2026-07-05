<?php

declare(strict_types=1);

namespace App\Events\Finance;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired when a payment completes an invoice (finance.invoicing,
 * event-bus contract). crm_account_id rides along for the CRM LTV
 * listener; null when the customer has no CRM link.
 */
class InvoicePaid
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $invoice_id,
        public readonly string $customer_id,
        public readonly int $total_cents,
        public readonly string $currency,
        public readonly ?string $crm_account_id,
    ) {}
}
