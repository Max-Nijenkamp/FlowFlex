<?php

declare(strict_types=1);

namespace App\Data\Finance;

use Spatie\LaravelData\Data;

class RecordPaymentData extends Data
{
    public function __construct(
        public readonly string $invoice_id,
        public readonly int $amount_cents,
        public readonly string $payment_date,
        public readonly string $payment_method,
        public readonly ?string $reference_number = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'invoice_id' => ['required', 'string'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:bank-transfer,stripe,cash,other'],
        ];
    }
}
