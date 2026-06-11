<?php

declare(strict_types=1);

namespace App\Events\Finance;

use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class ExpenseApproved
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $expense_id,
        public readonly ?string $employee_id,
        public readonly int $amount_cents,
        public readonly string $currency,
    ) {}
}
