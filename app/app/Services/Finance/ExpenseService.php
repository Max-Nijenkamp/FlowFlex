<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\ExpenseServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Data\Finance\SubmitExpenseData;
use App\Events\Finance\ExpenseApproved;
use App\Exceptions\Finance\CannotApproveOwnExpenseException;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\States\Finance\Expense\Approved;
use App\States\Finance\Expense\Reimbursed;
use App\States\Finance\Expense\Rejected;
use App\States\Finance\Expense\Submitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseService implements ExpenseServiceInterface
{
    public function __construct(
        private readonly LedgerServiceInterface $ledger,
    ) {}

    public function submit(SubmitExpenseData $data): Expense
    {
        $category = ExpenseCategory::query()->findOrFail($data->category_id);

        $expense = Expense::create([
            'user_id' => Auth::guard('web')->id(),
            'employee_id' => $data->employee_id,
            'category_id' => $category->id,
            'amount_cents' => $data->amount_cents,
            'expense_date' => $data->expense_date,
            'merchant' => $data->merchant,
            'description' => $data->description,
            'is_over_limit' => $category->limit_per_transaction_cents !== null
                && $data->amount_cents > $category->limit_per_transaction_cents,
        ]);

        $expense->status->transitionTo(Submitted::class);

        return $expense->refresh();
    }

    public function approve(string $expenseId): Expense
    {
        $expense = Expense::query()->findOrFail($expenseId);

        if ($expense->user_id === Auth::guard('web')->id()) {
            throw new CannotApproveOwnExpenseException('You cannot approve your own expense.');
        }

        return DB::transaction(function () use ($expense): Expense {
            $expense->status->transitionTo(Approved::class);
            $expense->forceFill(['approved_by' => Auth::guard('web')->id()])->save();

            // GL: operating expense up / reimbursable liability up.
            $this->ledger->post(
                reference: "EXP-{$expense->id}",
                description: "Expense approved: {$expense->merchant}",
                entryDate: now()->toDateString(),
                lines: [
                    ['account_code' => '6100', 'debit_cents' => $expense->amount_cents],
                    ['account_code' => '2300', 'credit_cents' => $expense->amount_cents],
                ],
                sourceType: Expense::class,
                sourceId: $expense->id,
            );

            event(new ExpenseApproved(
                company_id: $expense->company_id,
                expense_id: $expense->id,
                employee_id: $expense->employee_id,
                amount_cents: $expense->amount_cents,
                currency: $expense->currency,
            ));

            return $expense->refresh();
        });
    }

    public function reject(string $expenseId, string $reason): Expense
    {
        $expense = Expense::query()->findOrFail($expenseId);
        $expense->status->transitionTo(Rejected::class);
        $expense->forceFill(['description' => trim(($expense->description ?? '')."\nRejected: {$reason}")])->save();

        return $expense->refresh();
    }

    public function markReimbursed(string $expenseId, string $via): Expense
    {
        $expense = Expense::query()->findOrFail($expenseId);

        return DB::transaction(function () use ($expense, $via): Expense {
            $expense->status->transitionTo(Reimbursed::class);
            $expense->forceFill(['reimbursed_via' => $via])->save();

            // Liability cleared: reimbursements payable down / cash down.
            $this->ledger->post(
                reference: "REIMB-{$expense->id}",
                description: "Expense reimbursed via {$via}",
                entryDate: now()->toDateString(),
                lines: [
                    ['account_code' => '2300', 'debit_cents' => $expense->amount_cents],
                    ['account_code' => '1000', 'credit_cents' => $expense->amount_cents],
                ],
                sourceType: Expense::class,
                sourceId: $expense->id,
            );

            return $expense->refresh();
        });
    }
}
