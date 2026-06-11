<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Models\Finance\Budget;

interface BudgetServiceInterface
{
    /** @param array<array{account_id: string, period: string, budgeted_cents: int}> $lines */
    public function create(string $name, int $fiscalYear, array $lines, string $scopeType = 'company', ?string $scopeId = null): Budget;

    public function approve(string $budgetId): Budget;

    public function addLine(string $budgetId, string $accountId, string $period, int $budgetedCents): void;

    public function revise(string $budgetId): Budget;

    /** @return array<int, array{account_id: string, period: string, budgeted_cents: int, actual_cents: int, variance_cents: int}> */
    public function variance(string $budgetId, ?string $period = null): array;

    public function remaining(string $budgetId, string $accountId, string $period): int;
}
