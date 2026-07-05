<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\JournalLine;
use Brick\Money\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Statement import + reconciliation (finance.bank). Import dedupes on a
 * (date, amount, description) hash so re-uploading the same file is a
 * no-op; matching suggests exact-amount journal lines within ±5 days.
 */
class BankService
{
    /**
     * @param  list<array{date: string, description: string, amount: string|float}>  $rows
     * @return array{imported: int, skipped: int, errors: list<string>}
     */
    public function importRows(BankAccount $account, array $rows): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNo = $index + 1;

            try {
                $date = Carbon::parse($row['date']);
            } catch (\Throwable) {
                $errors[] = "Row {$lineNo}: unreadable date \"{$row['date']}\"";

                continue;
            }

            $description = trim($row['description']);
            if ($description === '') {
                $errors[] = "Row {$lineNo}: empty description";

                continue;
            }

            if (! is_numeric($row['amount'])) {
                $errors[] = "Row {$lineNo}: amount \"{$row['amount']}\" is not a number";

                continue;
            }

            $amountCents = Money::of((string) $row['amount'], $account->currency)
                ->getMinorAmount()->toInt();

            $hash = hash('sha256', $date->toDateString().'|'.$amountCents.'|'.$description);

            $exists = BankTransaction::query()
                ->where('bank_account_id', $account->id)
                ->where('import_hash', $hash)
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            BankTransaction::query()->create([
                'company_id' => $account->company_id,
                'bank_account_id' => $account->id,
                'transaction_date' => $date->toDateString(),
                'description' => $description,
                'amount_cents' => $amountCents,
                'import_hash' => $hash,
            ]);

            $account->current_balance_cents += $amountCents;
            $imported++;
        }

        $account->save();

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    /** @return array{imported: int, skipped: int, errors: list<string>} */
    public function importCsv(BankAccount $account, string $csv): array
    {
        $rows = [];

        foreach (preg_split('/\r\n|\r|\n/', trim($csv)) ?: [] as $line) {
            if (trim($line) === '') {
                continue;
            }

            $cells = str_getcsv($line);

            if (count($cells) < 3) {
                continue;
            }

            // Header row: skip anything whose amount cell is not numeric.
            if (! is_numeric($cells[2]) && strcasecmp(trim($cells[0]), 'date') === 0) {
                continue;
            }

            $rows[] = [
                'date' => trim((string) $cells[0]),
                'description' => trim((string) $cells[1]),
                'amount' => trim((string) $cells[2]),
            ];
        }

        return $this->importRows($account, $rows);
    }

    /**
     * Exact-amount journal lines on the account's GL account within ±5
     * days *(assumed window per spec)*, unreconciled transactions only.
     *
     * @return Collection<int, JournalLine>
     */
    public function suggestMatches(BankTransaction $transaction): Collection
    {
        $bankAccount = $transaction->bankAccount()->first();

        if (! $bankAccount instanceof BankAccount) {
            return collect();
        }

        $amount = abs($transaction->amount_cents);
        $side = $transaction->amount_cents >= 0 ? 'debit_cents' : 'credit_cents';

        $matchedLineIds = BankTransaction::query()
            ->whereNotNull('journal_line_id')
            ->pluck('journal_line_id');

        /** @var Collection<int, JournalLine> $matches */
        $matches = JournalLine::query()
            ->whereHas('entry', fn ($query) => $query
                ->whereDate('entry_date', '>=', $transaction->transaction_date->copy()->subDays(5))
                ->whereDate('entry_date', '<=', $transaction->transaction_date->copy()->addDays(5)))
            ->where('account_id', $bankAccount->gl_account_id)
            ->where($side, $amount)
            ->with('entry')
            ->get()
            ->reject(fn (JournalLine $line): bool => $matchedLineIds->contains($line->id))
            ->values();

        return $matches;
    }

    public function reconcile(BankTransaction $transaction, string $journalLineId): void
    {
        $transaction->update([
            'journal_line_id' => $journalLineId,
            'reconciled_at' => now(),
        ]);
    }

    public function unreconcile(BankTransaction $transaction): void
    {
        $transaction->update([
            'journal_line_id' => null,
            'reconciled_at' => null,
        ]);
    }

    /** @return array{bank_cents: int, ledger_cents: int, difference_cents: int} */
    public function balanceComparison(BankAccount $account): array
    {
        $ledger = JournalLine::query()
            ->where('account_id', $account->gl_account_id)
            ->selectRaw('COALESCE(SUM(debit_cents),0) - COALESCE(SUM(credit_cents),0) as balance')
            ->value('balance');

        $ledgerCents = (int) $ledger;

        return [
            'bank_cents' => $account->current_balance_cents,
            'ledger_cents' => $ledgerCents,
            'difference_cents' => $account->current_balance_cents - $ledgerCents,
        ];
    }
}
