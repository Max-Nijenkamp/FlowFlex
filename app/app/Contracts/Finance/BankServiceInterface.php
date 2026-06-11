<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\BankTransaction;
use App\Models\Finance\Invoice;
use Illuminate\Support\Collection;

interface BankServiceInterface
{
    /** CSV rows (date,description,amount_cents). Dedupes via import_hash; bad rows reported, never abort. */
    public function import(string $bankAccountId, string $csv): array;

    /** @return Collection<int, Invoice> exact-amount candidates ±5 days */
    public function suggestMatches(string $transactionId): Collection;

    /** Throws AmountMismatchException. */
    public function reconcile(string $transactionId, string $invoiceId): BankTransaction;
}
