<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Exceptions\Finance\ClosedPeriodException;
use App\Exceptions\Finance\UnbalancedEntryException;
use App\Models\Finance\Account;
use App\Models\Finance\FiscalPeriod;
use App\Models\Finance\JournalEntry;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

/**
 * The ONLY write path to the ledger. Entries are immutable once posted —
 * corrections happen via reverse().
 */
class LedgerService implements LedgerServiceInterface
{
    /**
     * @param  list<array{account_code: string, debit_cents?: int, credit_cents?: int, description?: string}>  $lines
     */
    public function post(
        string $reference,
        string $description,
        string $entryDate,
        array $lines,
        ?string $sourceType = null,
        ?string $sourceId = null,
    ): JournalEntry {
        $this->assertOpenPeriod($entryDate);

        $debits = array_sum(array_column($lines, 'debit_cents'));
        $credits = array_sum(array_column($lines, 'credit_cents'));

        if ($debits !== $credits || $debits === 0) {
            throw new UnbalancedEntryException(
                "Journal entry is unbalanced: debits {$debits} != credits {$credits}."
            );
        }

        return DB::transaction(function () use ($reference, $description, $entryDate, $lines, $sourceType, $sourceId): JournalEntry {
            $entry = JournalEntry::create([
                'reference' => $reference,
                'description' => $description,
                'entry_date' => $entryDate,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'created_by' => auth('web')->id(),
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create([
                    'company_id' => $entry->company_id,
                    'account_id' => $this->accountByCode($line['account_code'])->id,
                    'debit_cents' => $line['debit_cents'] ?? 0,
                    'credit_cents' => $line['credit_cents'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    public function reverse(string $journalEntryId, string $reason): JournalEntry
    {
        $original = JournalEntry::query()->with('lines.account')->findOrFail($journalEntryId);

        $mirrored = $original->lines->map(fn ($line) => [
            'account_code' => $line->account->code,
            'debit_cents' => $line->credit_cents,
            'credit_cents' => $line->debit_cents,
            'description' => "Reversal: {$reason}",
        ])->all();

        return $this->post(
            reference: "REV-{$original->reference}",
            description: "Reversal of {$original->reference}: {$reason}",
            entryDate: now()->toDateString(),
            lines: $mirrored,
            sourceType: JournalEntry::class,
            sourceId: $original->id,
        );
    }

    public function accountBalance(string $accountCode, string $currency = 'EUR'): Money
    {
        $account = $this->accountByCode($accountCode);

        $row = DB::table('fin_journal_lines')
            ->where('account_id', $account->id)
            ->selectRaw('COALESCE(SUM(debit_cents),0) as d, COALESCE(SUM(credit_cents),0) as c')
            ->first();

        // Debit-normal accounts (asset/expense) = debits - credits; others inverted.
        $net = in_array($account->type, ['asset', 'expense'], true)
            ? (int) $row->d - (int) $row->c
            : (int) $row->c - (int) $row->d;

        return Money::ofMinor($net, $currency);
    }

    public function closePeriod(string $period): void
    {
        FiscalPeriod::query()->updateOrCreate(
            ['period' => $period],
            ['status' => 'closed', 'closed_by' => auth('web')->id(), 'closed_at' => now()],
        );
    }

    public function reopenPeriod(string $period): void
    {
        FiscalPeriod::query()->where('period', $period)
            ->update(['status' => 'open', 'closed_by' => null, 'closed_at' => null]);
    }

    /** Default chart-of-accounts entries created on demand. */
    public function accountByCode(string $code): Account
    {
        $defaults = [
            '1000' => ['Cash & Bank', 'asset'],
            '1100' => ['Accounts Receivable', 'asset'],
            '2000' => ['Accounts Payable', 'liability'],
            '2100' => ['Wages Payable', 'liability'],
            '2200' => ['Withholdings Payable', 'liability'],
            '2300' => ['Reimbursements Payable', 'liability'],
            '4000' => ['Revenue', 'revenue'],
            '6000' => ['Wages Expense', 'expense'],
            '6100' => ['Operating Expenses', 'expense'],
        ];

        [$name, $type] = $defaults[$code] ?? ["Account {$code}", 'expense'];

        return Account::query()->firstOrCreate(['code' => $code], ['name' => $name, 'type' => $type]);
    }

    private function assertOpenPeriod(string $entryDate): void
    {
        $period = date('Y-m', strtotime($entryDate));

        $closed = FiscalPeriod::query()
            ->where('period', $period)
            ->where('status', 'closed')
            ->exists();

        if ($closed) {
            throw new ClosedPeriodException("Fiscal period {$period} is closed.");
        }
    }
}
