<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\BudgetServiceInterface;
use App\Exceptions\Finance\BudgetApprovedException;
use App\Models\Finance\Budget;
use App\Models\Finance\JournalLine;
use Illuminate\Support\Facades\DB;

class BudgetService implements BudgetServiceInterface
{
    /** @param array<array{account_id: string, period: string, budgeted_cents: int}> $lines */
    public function create(string $name, int $fiscalYear, array $lines, string $scopeType = 'company', ?string $scopeId = null): Budget
    {
        return DB::transaction(function () use ($name, $fiscalYear, $lines, $scopeType, $scopeId): Budget {
            $budget = Budget::create([
                'name' => $name,
                'fiscal_year' => $fiscalYear,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
            ])->refresh();

            foreach ($lines as $line) {
                $budget->lines()->create([
                    'company_id' => $budget->company_id,
                    'account_id' => $line['account_id'],
                    'period' => $line['period'],
                    'budgeted_cents' => $line['budgeted_cents'],
                ]);
            }

            return $budget;
        });
    }

    public function approve(string $budgetId): Budget
    {
        $budget = Budget::query()->findOrFail($budgetId);
        $budget->update(['status' => 'approved']);

        return $budget->refresh();
    }

    /** Approved budgets are immutable — line edits must go through a new version. */
    public function addLine(string $budgetId, string $accountId, string $period, int $budgetedCents): void
    {
        $budget = Budget::query()->findOrFail($budgetId);

        if ($budget->status === 'approved') {
            throw new BudgetApprovedException;
        }

        $budget->lines()->create([
            'company_id' => $budget->company_id,
            'account_id' => $accountId,
            'period' => $period,
            'budgeted_cents' => $budgetedCents,
        ]);
    }

    /** New version row, copies lines; old version preserved. */
    public function revise(string $budgetId): Budget
    {
        return DB::transaction(function () use ($budgetId): Budget {
            $old = Budget::query()->with('lines')->findOrFail($budgetId);

            $new = Budget::create([
                'name' => $old->name,
                'fiscal_year' => $old->fiscal_year,
                'scope_type' => $old->scope_type,
                'scope_id' => $old->scope_id,
                'version' => $old->version + 1,
            ])->refresh();

            foreach ($old->lines as $line) {
                $new->lines()->create([
                    'company_id' => $new->company_id,
                    'account_id' => $line->account_id,
                    'period' => $line->period,
                    'budgeted_cents' => $line->budgeted_cents,
                ]);
            }

            return $new;
        });
    }

    /**
     * Budget vs actual per account/period; actuals from journal lines.
     *
     * @return array<int, array{account_id: string, period: string, budgeted_cents: int, actual_cents: int, variance_cents: int}>
     */
    public function variance(string $budgetId, ?string $period = null): array
    {
        $budget = Budget::query()->with('lines')->findOrFail($budgetId);
        $rows = [];

        foreach ($budget->lines as $line) {
            if ($period !== null && $line->period !== $period) {
                continue;
            }

            $actual = $this->actualFor($line->account_id, $line->period);

            $rows[] = [
                'account_id' => $line->account_id,
                'period' => $line->period,
                'budgeted_cents' => $line->budgeted_cents,
                'actual_cents' => $actual,
                'variance_cents' => $actual - $line->budgeted_cents,
            ];
        }

        return $rows;
    }

    /** Remaining budget for an account/period — consumed by procurement/workforce checks. */
    public function remaining(string $budgetId, string $accountId, string $period): int
    {
        $budget = Budget::query()->findOrFail($budgetId);
        $budgeted = (int) $budget->lines()
            ->where('account_id', $accountId)
            ->where('period', $period)
            ->sum('budgeted_cents');

        return $budgeted - $this->actualFor($accountId, $period);
    }

    private function actualFor(string $accountId, string $period): int
    {
        [$year, $month] = explode('-', $period);

        return (int) JournalLine::query()
            ->where('account_id', $accountId)
            ->whereHas('entry', fn ($q) => $q
                ->whereYear('entry_date', (int) $year)
                ->whereMonth('entry_date', (int) $month))
            ->selectRaw('COALESCE(SUM(debit_cents - credit_cents), 0) as net')
            ->value('net');
    }
}
