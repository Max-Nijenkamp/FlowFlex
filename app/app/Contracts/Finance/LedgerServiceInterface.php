<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\JournalEntry;
use Brick\Money\Money;

interface LedgerServiceInterface
{
    /**
     * Validates balance + open period inside a transaction.
     * Throws UnbalancedEntryException, ClosedPeriodException.
     *
     * @param  list<array{account_code: string, debit_cents?: int, credit_cents?: int, description?: string}>  $lines
     */
    public function post(string $reference, string $description, string $entryDate, array $lines, ?string $sourceType = null, ?string $sourceId = null): JournalEntry;

    /** Mirrored entry; original untouched. */
    public function reverse(string $journalEntryId, string $reason): JournalEntry;

    public function accountBalance(string $accountCode, string $currency = 'EUR'): Money;

    public function closePeriod(string $period): void;

    public function reopenPeriod(string $period): void;
}
