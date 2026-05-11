<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class CreatePayrollRunData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $period_start,
        public readonly string $period_end,
        public readonly string $pay_date,
        public readonly string $currency = 'EUR',
    ) {}
}
