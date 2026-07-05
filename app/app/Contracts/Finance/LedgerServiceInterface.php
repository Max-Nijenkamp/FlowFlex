<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\JournalEntry;
use DateTimeInterface;
use Illuminate\Support\Collection;

interface LedgerServiceInterface
{
    /**
     * Post a balanced journal entry — the only sanctioned write path for
     * financial truth.
     *
     * @param  list<array{account_id: string, debit_cents?: int, credit_cents?: int, description?: string}>  $lines
     */
    public function post(
        string $reference,
        string $description,
        DateTimeInterface $entryDate,
        array $lines,
        ?string $sourceType = null,
        ?string $sourceId = null,
    ): JournalEntry;

    /** Corrections happen via mirrored reversals — posted entries never mutate. */
    public function reverse(string $journalEntryId, ?string $reason = null): JournalEntry;

    /** @return Collection<int, array{account: Account, debit_cents: int, credit_cents: int}> */
    public function trialBalance(DateTimeInterface $from, DateTimeInterface $until): Collection;
}
