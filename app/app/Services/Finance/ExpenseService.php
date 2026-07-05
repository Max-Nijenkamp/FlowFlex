<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\ExpenseReport;
use App\Models\User;
use App\States\Finance\Expense\Approved;
use App\States\Finance\Expense\Reimbursed;
use App\States\Finance\Expense\Rejected;
use App\States\Finance\Expense\Submitted;
use App\Support\Services\AuditLogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Expense approval workflow (finance.expenses): draft → submitted →
 * approved|rejected → reimbursed. The category is the policy unit —
 * over-limit claims are flagged, never blocked *(assumed)*; the journal
 * posts on reimbursement (expense ↑ / bank ↓) via LedgerService.
 */
class ExpenseService
{
    public function __construct(private readonly LedgerServiceInterface $ledger) {}

    public function submit(Expense $expense): Expense
    {
        $category = $expense->category()->first();

        $expense->is_over_limit = $category instanceof ExpenseCategory
            && $category->limit_per_transaction_cents !== null
            && $expense->amount_cents > $category->limit_per_transaction_cents;

        $expense->status->transitionTo(Submitted::class);
        $expense->save();

        return $expense->refresh();
    }

    public function approve(Expense $expense): Expense
    {
        if ($expense->user_id === Auth::id()) {
            throw ValidationException::withMessages(['expense' => 'You cannot approve your own expense.']);
        }

        $expense->status->transitionTo(Approved::class);
        $expense->update(['approved_by' => Auth::id()]);

        $causer = Auth::user();
        app(AuditLogger::class)->log(
            'finance.expense-approved',
            $expense,
            $causer instanceof User ? $causer : null,
            ['amount_cents' => $expense->amount_cents, 'over_limit' => $expense->is_over_limit],
        );

        return $expense->refresh();
    }

    public function reject(Expense $expense, string $reason): Expense
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages(['reason' => 'A rejection reason is required.']);
        }

        $expense->status->transitionTo(Rejected::class);
        $expense->update(['rejection_reason' => $reason]);

        return $expense->refresh();
    }

    public function reimburse(Expense $expense, string $via = 'bank-transfer'): Expense
    {
        return DB::transaction(function () use ($expense, $via): Expense {
            $expense->status->transitionTo(Reimbursed::class);
            $expense->update(['reimbursed_via' => $via]);

            $category = $expense->category()->firstOrFail();

            $this->ledger->post(
                reference: 'EXP-'.strtoupper(substr($expense->id, -6)),
                description: "Expense reimbursement — {$expense->merchant}",
                entryDate: now(),
                lines: [
                    ['account_id' => $category->gl_account_id, 'debit_cents' => $expense->amount_cents],
                    ['account_id' => LedgerService::accountIdByCode('1100'), 'credit_cents' => $expense->amount_cents],
                ],
                sourceType: 'expense',
                sourceId: $expense->id,
            );

            return $expense->refresh();
        });
    }

    /** Bulk submit: cascades to every draft expense on the report. */
    public function submitReport(ExpenseReport $report): ExpenseReport
    {
        return DB::transaction(function () use ($report): ExpenseReport {
            /** @var Collection<int, Expense> $drafts */
            $drafts = $report->expenses()->where('status', 'draft')->get();

            foreach ($drafts as $expense) {
                $this->submit($expense);
            }

            $report->update(['status' => 'submitted', 'submitted_at' => now()]);

            return $report->refresh();
        });
    }
}
