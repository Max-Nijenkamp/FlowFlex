<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\ArWriteoff;
use App\Models\Finance\Invoice;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

interface ArServiceInterface
{
    /** @return array<string, int> */
    public function aging(?string $customerId = null): array;

    /** @return Collection<int, Invoice> */
    public function statement(string $customerId, CarbonImmutable $from, CarbonImmutable $to): Collection;

    public function writeOff(string $invoiceId, string $reason): ArWriteoff;

    /** @param array<array{invoice_id: string, amount_cents: int}> $allocations */
    public function allocatePayment(array $allocations, string $paymentDate, string $method = 'bank-transfer'): void;

    public function dso(CarbonImmutable $from, CarbonImmutable $to): float;

    public function runDunning(): int;
}
