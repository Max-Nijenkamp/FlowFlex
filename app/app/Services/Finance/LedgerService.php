<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Exceptions\Finance\ClosedPeriodException;
use App\Exceptions\Finance\UnbalancedEntryException;
use App\Models\Finance\Account;
use App\Models\Finance\FiscalPeriod;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalLine;
use App\Support\Services\CompanyContext;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The only sanctioned write path for financial truth (finance.ledger).
 * Entries must balance to the cent; posting into a closed period is
 * rejected; posted entries are immutable — reverse() mirrors them.
 */
class LedgerService implements LedgerServiceInterface
{
    public function post(
        string $reference,
        string $description,
        DateTimeInterface $entryDate,
        array $lines,
        ?string $sourceType = null,
        ?string $sourceId = null,
    ): JournalEntry {
        $companyId = app(CompanyContext::class)->current()->id;
        $date = Carbon::instance(Carbon::parse($entryDate));

        if ($lines === []) {
            throw new InvalidArgumentException('A journal entry needs at least two lines.');
        }

        $debits = 0;
        $credits = 0;
        foreach ($lines as $line) {
            $debit = (int) ($line['debit_cents'] ?? 0);
            $credit = (int) ($line['credit_cents'] ?? 0);

            if (($debit === 0) === ($credit === 0)) {
                throw new InvalidArgumentException('Each line carries exactly one non-zero side.');
            }

            $debits += $debit;
            $credits += $credit;
        }

        if ($debits !== $credits) {
            throw UnbalancedEntryException::make();
        }

        if (FiscalPeriod::isClosed($companyId, $date)) {
            throw ClosedPeriodException::make();
        }

        return DB::transaction(function () use ($companyId, $reference, $description, $date, $lines, $sourceType, $sourceId): JournalEntry {
            /** @var JournalEntry $entry */
            $entry = JournalEntry::query()->create([
                'company_id' => $companyId,
                'reference' => $reference,
                'description' => $description,
                'entry_date' => $date->toDateString(),
                'status' => 'posted',
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'created_by' => Auth::id(),
            ]);

            foreach ($lines as $line) {
                JournalLine::query()->create([
                    'company_id' => $companyId,
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit_cents' => (int) ($line['debit_cents'] ?? 0),
                    'credit_cents' => (int) ($line['credit_cents'] ?? 0),
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    public function reverse(string $journalEntryId, ?string $reason = null): JournalEntry
    {
        /** @var JournalEntry $original */
        $original = JournalEntry::query()->with('lines')->findOrFail($journalEntryId);

        /** @var \Illuminate\Database\Eloquent\Collection<int, JournalLine> $originalLines */
        $originalLines = $original->lines()->get();

        $mirrored = $originalLines->map(fn (JournalLine $line): array => [
            'account_id' => $line->account_id,
            'debit_cents' => $line->credit_cents,
            'credit_cents' => $line->debit_cents,
            'description' => $line->description,
        ])->all();

        return $this->post(
            reference: $original->reference.'-REV',
            description: $reason ?? "Reversal of {$original->reference}",
            entryDate: now(),
            lines: $mirrored,
            sourceType: 'reversal',
            sourceId: $original->id,
        );
    }

    public function trialBalance(DateTimeInterface $from, DateTimeInterface $until): Collection
    {
        $rows = JournalLine::query()
            ->whereHas('entry', fn ($query) => $query
                ->whereDate('entry_date', '>=', $from)
                ->whereDate('entry_date', '<=', $until))
            ->selectRaw('account_id, SUM(debit_cents) as total_debit, SUM(credit_cents) as total_credit')
            ->groupBy('account_id')
            ->get();

        /** @var \Illuminate\Database\Eloquent\Collection<string, Account> $accounts */
        $accounts = Account::query()->whereIn('id', $rows->pluck('account_id'))->get()->keyBy('id');

        return $rows
            ->map(fn ($row): array => [
                'account' => $accounts->get((string) $row->account_id),
                'debit_cents' => (int) $row->total_debit,
                'credit_cents' => (int) $row->total_credit,
            ])
            ->filter(fn (array $row): bool => $row['account'] instanceof Account)
            ->sortBy(fn (array $row): string => $row['account']->code)
            ->values();
    }

    /** Default SME chart, seeded on activation — idempotent by (company, code). */
    public static function ensureDefaultChartOfAccounts(string $companyId): void
    {
        $defaults = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Bank', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Accounts receivable', 'type' => 'asset'],
            ['code' => '1500', 'name' => 'Equipment', 'type' => 'asset'],
            ['code' => '2000', 'name' => 'Accounts payable', 'type' => 'liability'],
            ['code' => '2100', 'name' => 'Accrued liabilities', 'type' => 'liability'],
            ['code' => '2200', 'name' => 'VAT payable', 'type' => 'liability'],
            ['code' => '3000', 'name' => 'Owner equity', 'type' => 'equity'],
            ['code' => '4000', 'name' => 'Sales revenue', 'type' => 'revenue'],
            ['code' => '5000', 'name' => 'Cost of goods sold', 'type' => 'expense'],
            ['code' => '6000', 'name' => 'Operating expenses', 'type' => 'expense'],
            ['code' => '6100', 'name' => 'Travel & transport', 'type' => 'expense'],
            ['code' => '6200', 'name' => 'Office & supplies', 'type' => 'expense'],
        ];

        foreach ($defaults as $account) {
            Account::query()->firstOrCreate(
                ['company_id' => $companyId, 'code' => $account['code']],
                ['name' => $account['name'], 'type' => $account['type']],
            );
        }
    }

    public static function accountIdByCode(string $code): string
    {
        return Account::query()->where('code', $code)->firstOrFail()->id;
    }
}
