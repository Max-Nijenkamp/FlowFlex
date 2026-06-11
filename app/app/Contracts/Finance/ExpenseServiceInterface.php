<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Data\Finance\SubmitExpenseData;
use App\Models\Finance\Expense;

interface ExpenseServiceInterface
{
    public function submit(SubmitExpenseData $data): Expense;

    /** Four-eyes; fires ExpenseApproved + GL posting. */
    public function approve(string $expenseId): Expense;

    public function reject(string $expenseId, string $reason): Expense;

    public function markReimbursed(string $expenseId, string $via): Expense;
}
