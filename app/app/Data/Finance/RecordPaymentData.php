<?php

declare(strict_types=1);

namespace App\Data\Finance;

use Spatie\LaravelData\Data;

class RecordPaymentData extends Data
{
    public function __construct(
        public string $invoiceId,
        public int $amountCents,
        public ?string $paymentDate = null,
        public ?string $method = null,
        public ?string $reference = null,
    ) {}
}
