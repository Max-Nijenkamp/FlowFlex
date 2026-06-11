<?php

declare(strict_types=1);

namespace App\Events\CRM;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class DealWon
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $deal_id,
        public readonly ?string $account_id,
        public readonly ?string $contact_id,
        public readonly string $owner_id,
        public readonly int $value_cents,
        public readonly string $currency,
        public readonly CarbonImmutable $won_at,
    ) {}
}
