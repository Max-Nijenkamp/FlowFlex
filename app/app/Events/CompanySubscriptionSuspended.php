<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** Cross-domain event — payload per architecture/event-bus. company_id always a scalar. */
class CompanySubscriptionSuspended
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $reason,
    ) {}
}
