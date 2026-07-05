<?php

declare(strict_types=1);

namespace App\Events\Crm;

use Illuminate\Foundation\Events\Dispatchable;

/** Fired by DealService::close on the won path (event-bus contract). */
class DealWon
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $deal_id,
        public readonly ?string $account_id,
        public readonly ?string $contact_id,
        public readonly int $value_cents,
        public readonly string $currency,
        public readonly string $name,
    ) {}
}
