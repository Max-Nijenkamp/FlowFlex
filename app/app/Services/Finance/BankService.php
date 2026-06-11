<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\BankServiceInterface;
use App\Exceptions\Finance\AmountMismatchException;
use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\Invoice;
use Illuminate\Support\Collection;

class BankService implements BankServiceInterface
{
    public function import(string $bankAccountId, string $csv): array
    {
        $account = BankAccount::query()->findOrFail($bankAccountId);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach (array_filter(array_map('trim', explode("\n", $csv))) as $i => $line) {
            $parts = str_getcsv($line);
            if (count($parts) < 3 || ! is_numeric($parts[2])) {
                $errors[] = ['row' => $i + 1, 'error' => 'Expected date,description,amount_cents'];

                continue;
            }

            [$date, $description, $amount] = $parts;
            $hash = hash('sha256', "{$date}|{$description}|{$amount}");

            $created = BankTransaction::query()->firstOrCreate(
                ['bank_account_id' => $account->id, 'import_hash' => $hash],
                [
                    'company_id' => $account->company_id,
                    'transaction_date' => $date,
                    'description' => $description,
                    'amount_cents' => (int) $amount,
                ],
            );

            $created->wasRecentlyCreated ? $imported++ : $skipped++;
        }

        $account->increment('current_balance_cents', BankTransaction::query()
            ->where('bank_account_id', $account->id)
            ->whereNull('reconciled_at')
            ->count() === 0 ? 0 : 0); // balance recomputed below

        $account->update([
            'current_balance_cents' => (int) BankTransaction::query()
                ->where('bank_account_id', $account->id)
                ->sum('amount_cents'),
        ]);

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    public function suggestMatches(string $transactionId): Collection
    {
        $transaction = BankTransaction::query()->findOrFail($transactionId);

        return Invoice::query()
            ->where('total_cents', abs($transaction->amount_cents))
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->whereBetween('due_date', [
                $transaction->transaction_date->copy()->subDays(5),
                $transaction->transaction_date->copy()->addDays(5),
            ])
            ->get();
    }

    public function reconcile(string $transactionId, string $invoiceId): BankTransaction
    {
        $transaction = BankTransaction::query()->findOrFail($transactionId);
        $invoice = Invoice::query()->findOrFail($invoiceId);

        if (abs($transaction->amount_cents) !== $invoice->total_cents - $invoice->paid_amount_cents) {
            throw new AmountMismatchException('Transaction amount does not match the invoice balance.');
        }

        $transaction->update(['reconciled_at' => now()]);

        return $transaction->refresh();
    }
}
