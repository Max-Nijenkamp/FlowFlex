<?php

declare(strict_types=1);

namespace App\Events\Crm;

use Illuminate\Foundation\Events\Dispatchable;

/** Fired by DealService::close on the lost path (event-bus contract). */
class DealLost
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $deal_id,
        public readonly string $lost_reason,
        public readonly ?string $lost_to,
    ) {}
}
