<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\Bill;
use App\Models\Finance\PaymentRun;

interface ApServiceInterface
{
    /** @param array<array{description: string, account_code: string, amount_cents: int}> $lines */
    public function createBill(string $supplierId, string $billNumber, string $billDate, string $dueDate, array $lines, ?float $earlyDiscountPercent = null, ?string $earlyDiscountUntil = null): Bill;

    public function approveBill(string $billId): Bill;

    /** @param array<string> $billIds */
    public function createPaymentRun(string $runDate, array $billIds): PaymentRun;

    public function executeRun(string $runId): PaymentRun;

    /** @return array<string, int> */
    public function aging(): array;
}
