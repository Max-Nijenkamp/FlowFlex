<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** Events always carry company_id as a typed scalar, never a model. */
class CompanySubscriptionSuspended
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $reason,
    ) {}
}
